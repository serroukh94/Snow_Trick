<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{

    private $entityManager;   // le manager de doctrine dont t'on se sert pour faire nos manipulation

    public function __construct(EntityManagerInterface $entityManager)  {
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'register')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $notification = null;


        $user = new User();   //instancier ma class user car l'utilisateur qui arrive sur la page inscription c'est un utilisateur qui veut s'inscrire
        $form = $this->createForm(RegisterType::class, $user);  // instancier mon formulaire


        // des que ce formulaire est soumis, symfony traite l'information et ensuite si tout va bien enregistre en BDD
        $form -> handleRequest($request);  // la methode handleRequest permet a symfony de manipuler l'objet requete pour verifier a l'interieur qu'on n'a pas de post

        if ($form -> isSubmitted() && $form->isValid() )  {  // verification si le formulaire est bien soumis et valide par rapport a contrainte de notre fichier RegisterType.php

            $user = $form->getData();  // injection dans l'objet user toutes les donnees qu'on recupére du formulaire

            $search_email=$this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email)
            { $password = $hasher->hashPassword($user,$user->getPassword());

                $user ->setPassword($password);

                //on génère le token d'activation
                $user->setToken(md5(uniqid()));




                //enregistrer les information sur notre BDD avec doctrine

                $this->entityManager->persist($user);   // la methode persist fige la data de notre entité user
                $this->entityManager->flush();          // la methode flush sert a enregistré la data en BDD qu'on a figer.


                $mail = new Mail();
                $content = "Bonjour".$user->getFirstname()."<br/>Bienvenue sur SnowTrick.<br><br/>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. <br/>";
                $mail-> send($user->getEmail(), $user->getFirstname(), 'Bienvenue sur SnowTrick', $content);

                $this->addFlash('success', 'Votre insription est correctement déroulée. Vous pouvez dés à présent vous connecter à votre compte.');


            }else{
                $this->addFlash('success', "l'email que vous avez renseigné existe déjà.");


            }
            return $this->redirectToRoute('app_login');

        }


        return $this->render('register/index.html.twig', [
            'form'=>$form->createView(),
            'notification' => $notification,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/mot-de-passe-oublie/password', name: 'reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email'))
        {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user)
            {
                // 1 : enregistrer la demande de reset_password avec user, token, createdAt.
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                // 2 : Envoyer un email à l'utilisateur avec un lien lui permetant de mettre a jour son mot de passe.

                $url =$this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content  = "Bonjour" .$user->getFirstname().",<br/>Vous avez demandé de réinitialiser votre mot de passe sur le site SnowTrick. <br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'> mettre à jour votre mot de passe</a>." ;

                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname(). ' '.$user->getLastname(),'Réinitialiser Votre mot de passe sur SnowTrick', $content);

                $this->addFlash('success', 'Vous allez recevoir dans quelques secondes un mail avec la procédure pour reinitialiser votre mot de passe.');

            }else{
                $this->addFlash('success', 'Cette adresse email est inconnue.');
            }

        }


        return $this->render('reset_password/index.html.twig');
    }



    #[Route('/modifier-mon-mot-de-passe/{token} ', name: 'update_password')]
    public function update(Request $request, $token, UserPasswordHasherInterface $hasher): Response
    {
        $reset_password =$this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);
        if (!$reset_password){
            return $this->redirectToRoute('reset_password');
        }

        //verifier si le createdAt = now - 3h
        $now = new \DateTime();
        if($now > $reset_password->getcreatedAt()->modify('+ 3 hour')) {

            $this->addFlash('success', 'Votre demande de mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('reset_password');

        }
        //Rendre une vue avec mot de passe confirmez votre mot de passe.

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $new_pwd = $form->get('new_password')->getData();

            //Encodage de mot de passe
            $password = $hasher->hashPassword($reset_password->getUser(), $new_pwd);
            $reset_password->getUser()->setPassword($password);

            //Flush en basse de donnée
            $this->entityManager->flush();

            //Redirection de l'utilisateur vers la page connexion
            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('home');

        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);



    }
}

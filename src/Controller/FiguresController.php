<?php

namespace App\Controller;


use App\Entity\Figures;
use App\Entity\Images;
use App\Entity\Video;
use App\Form\FiguresType;
use App\Repository\FiguresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FiguresController extends AbstractController
{

    #[Route('/figures', name: 'figures_index', methods: ['GET'])]
    public function index(FiguresRepository $figuresRepository): Response
    {
        return $this->render('figures/index.html.twig', [
            'figures' => $figuresRepository->findAll(),
        ]);
    }

    #[Route('/figures/figure/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $figure = new Figures();
        //Je crée le formulaire
        $form = $this->createForm(FiguresType::class, $figure);
        //Je lance la requete
        $form->handleRequest($request);
        //Je vérifie le formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach ($images as $image){
                //On génére un nouveau nom de fichier
                $fichier = md5(uniqid()). '.' . $image->guessExtension();

                //On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                //On stocke l'image dans la base de données (son nom)
                $img = new Images();
                $img->setName($fichier);
                $figure->addImage($img);
            }

            if ($form->get('videos')->getData() !== null) {
                $video = new video();
                $video->setUrl($form->get('videos')->getData());
                $video->setFigure($figure);
                $entityManager->persist($video);
                $figure->addVideo($video);

            }
            if ($form->get('title')->getData() !== $figure->getTitle()) {
                $figure->setTitle($form->get('title')->getData());

            }


            $figure->setCreatedAt(new \DateTimeImmutable())
                    ->setUser($this->getUser());

            //Je persist
            $entityManager->persist($figure);
            //Je lance l'enregistrement
            $entityManager->flush();

            $this->addFlash('success', 'La figure a bien été créé !');

            //Redirection
            return $this->redirectToRoute('figures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figures/new.html.twig', [
            'figure' => $figure,
            'form'   => $form,
        ]);
    }



    #[Route('/figures/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Figures $figure, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FiguresType::class, $figure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach ($images as $image){
                //On génére un nouveau nom de fichier
                $fichier = md5(uniqid()). '.' . $image->guessExtension();
                //On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                //On stocke l'image dans la base de données (son nom)
                $img = new Images();
                $img->setName($fichier);
                $figure->addImage($img);
            }

            if ($form->get('videos')->getData() !== null) {
                $video = new video();
                $video->setUrl($form->get('videos')->getData());
                $video->setFigure($figure);
                $entityManager->persist($video);
                $figure->addVideo($video);

            }
            if ($form->get('title')->getData() !== $figure->getTitle()) {
                $figure->setTitle($form->get('title')->getData());

            }

            $figure->setUser($this->getUser());

            $entityManager->persist($figure);
            $entityManager->flush();

            $this->addFlash('success', 'La figure a bien été modifié !');

            return $this->redirectToRoute('figures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figures/edit.html.twig', [
            'figure' => $figure,
            'form' => $form,
        ]);
    }

    #[Route('/figures/{id}/delete', name: 'figures_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Figures $figure, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete'.$figure->getId(), $request->request->get('_token'))) {
            $entityManager->remove($figure);
            $entityManager->flush();
        }

        $this->addFlash('success', 'La figure a bien été supprimé !');

        return $this->redirectToRoute('figures_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/figures/supprime/image/{id}', name: 'figures_delete_image', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $entityManager){

        $data = json_decode($request->getContent(), true);

        // on verifie si un token est valide
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            //on recupere le nom d'image
            $nom = $image->getName();
            //on supprime le fichier
            unlink($this->getParameter('images_directory').'/'.$nom);

            // on supprime l'entree de la base
            $entityManager->remove($image);
            $entityManager->flush();

            // on repond en json
            return new  JsonResponse(['success'=> 1]);
        }else{
            return new JsonResponse(['error'=> 'Token Invalide'], 400);
        }
    }


    #[Route('/figures/supprime/video/{id}', name: 'deleteVid', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteVid(Video $video, EntityManagerInterface $entityManager, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // on verifie si un token est valide
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $data['_token'])){
            //on recupere le nom d'image
            $nom = $video->getUrl();


            // on supprime l'entree de la base
            $entityManager->remove($video);
            $entityManager->flush();


            // on repond en json
            return new  JsonResponse(['success'=> 1]);
        }else{
            return new JsonResponse(['error'=> 'Token Invalide'], 400);
        }
    }

}

<?php

namespace App\Controller;

use App\Entity\Figures;
use App\Entity\Images;
use App\Form\FiguresType;
use App\Repository\FiguresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figures')]
class FiguresController extends AbstractController
{
    #[Route('/', name: 'figures_index', methods: ['GET'])]
    public function index(FiguresRepository $figuresRepository): Response
    {
        return $this->render('figures/index.html.twig', [
            'figures' => $figuresRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'figures_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $figure = new Figures();
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


            $entityManager->persist($figure);
            $entityManager->flush();

            return $this->redirectToRoute('figures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figures/new.html.twig', [
            'figure' => $figure,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'figures_show', methods: ['GET'])]
    public function show(Figures $figure): Response
    {
        return $this->render('figures/show.html.twig', [
            'figure' => $figure,
        ]);
    }

    #[Route('/{id}/edit', name: 'figures_edit', methods: ['GET', 'POST'])]
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

            $entityManager->flush();

            return $this->redirectToRoute('figures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('figures/edit.html.twig', [
            'figure' => $figure,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'figures_delete', methods: ['POST'])]
    public function delete(Request $request, Figures $figure, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$figure->getId(), $request->request->get('_token'))) {
            $entityManager->remove($figure);
            $entityManager->flush();
        }

        return $this->redirectToRoute('figures_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/supprime/image/{id}', name: 'figures_delete_image', methods: ['DELETE'])]
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
}

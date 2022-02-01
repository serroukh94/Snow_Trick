<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Form\FigureType;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figure')]
class FigureController extends AbstractController
{


    #[Route('/figures', name: 'figures')]

    public function index(FigureRepository $repository): Response
    {
        //recuperer mes figurer


        $figures = $repository->findAll();

        return $this->render('figure/figures.html.twig',[
            'figures' => $figures,    //  j'ai créer cette variable figures va me permmetre d'afficher nos figure au coté twig.
        ]);
    }



    #[Route('/figure/creation', name: 'figure_creation')]
    #[Route('/figure/{id}', name: 'figure_modification')]

    public function ajoutEtModif(Figure $figure = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$figure){
            $figure = new Figure();
        }

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($figure);
            $entityManager->flush();
            return $this->redirectToRoute('figures');
        }

        return $this->render('figure/ajoutEtModif.html.twig', [
            "figure"=> $figure,
            "form"  =>$form->createView(),
            "isModification" => $figure->getId() !== null
        ]);
    }

}

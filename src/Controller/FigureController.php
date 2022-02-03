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
    #[Route('/figure/{id}', name: 'figure_modification', methods: 'GET|POST')]

    public function ajoutEtModif(Figure $figure = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$figure){
            $figure = new Figure();
        }

        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $modif = $figure->getId() !== null;
            $entityManager->persist($figure);
            $entityManager->flush();
            $this->addFlash('success', ($modif) ? 'la modification a été effectuée' : 'L\'ajout a été effectuée'); //  j'ai mis en place la Ternaire pour un true ou false pour bien affiché le flash correspondant de l'ajout ou bien modif
            return $this->redirectToRoute('figures');
        }

        return $this->render('figure/ajoutEtModif.html.twig', [
            "figure"=> $figure,
            "form"  =>$form->createView(),
            "isModification" => $figure->getId() !== null
        ]);
    }


    #[Route('/figure/{id}', name: 'figure_suppression', methods: 'delete')]

    public function suppression(Figure $figure, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('SUP'. $figure->getId(), $request->get('_token'))){
            $entityManager->remove($figure);
            $entityManager->flush();
            $this->addFlash('success', 'la suppression a été effectuée');
            return $this->redirectToRoute('figures');
        }

    }

}

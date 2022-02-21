<?php

namespace App\Controller;


use App\Entity\Comments;
use App\Entity\Figures;
use App\Form\CommentType;
use App\Repository\FiguresRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(FiguresRepository $repository): Response
    {
        $figures = $repository->findAll();

        return $this->render('home/home.html.twig',[
            'figures' => $figures,
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET', 'POST'])]
    public function show(Figures $figure, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comments();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setFigure($figure);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', "Votre commentaire a été ajouté à la liste de discussion de cette figure.");
        }

        return $this->render('home/show.html.twig', [
            'figure' => $figure,
            'form' => $form->createView(),
        ]);
    }
}

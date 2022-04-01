<?php

namespace App\Controller;


use App\Entity\Comments;
use App\Entity\Figures;
use App\Entity\Header;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\FiguresRepository;
use App\Repository\HeaderRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'home')]
    public function index(FiguresRepository $repository): Response
    {
        $figures = $repository->findAll();
        $headers  = $this->entityManager->getRepository(Header::class)->findAll();

        return $this->render('home/home.html.twig',[
            'figures' => $figures,
            'headers' => $headers
        ]);
    }

    #[Route('/figures/{slug}', name: 'show', methods: ['GET', 'POST'])]
    public function show(Figures $figure, Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepo, $page = 1): Response
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

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepo->getCommentPaginator($figure, $offset);

        return $this->render('home/show.html.twig', [
            'figure' => $figure,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE),
            'form' => $form->createView()
        ]);
    }
}

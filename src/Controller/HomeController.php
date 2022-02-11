<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Figures;
use App\Repository\FiguresRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}

<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\Figure;
use App\Repository\FigureRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(FigureRepository $repository): Response
    {
        $figures = $repository->findAll();

        return $this->render('home/home.html.twig',[
            'figures' => $figures,
        ]);
    }
}

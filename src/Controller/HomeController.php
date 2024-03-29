<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(name: 'home_')]
class HomeController extends AbstractController
{
    #[Route(path: '')]
    #[Route(path: 'home', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }
}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu', name: 'app_lieu')]
    public function index(): Response
    {
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }
        #[Route('/Site', name: 'app_Site')]
    public function Site(): Response
    {

        return $this->render('lieu/Site.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }

}

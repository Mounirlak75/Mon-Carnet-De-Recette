<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PolitiqueDeConfidentialitéController extends AbstractController
{
    #[Route('/politique/de/confidentialit/', name: 'app_politique_de_confidentialit_')]
    public function index(): Response
    {
        return $this->render('politique_de_confidentialité/index.html.twig', [
            'controller_name' => 'PolitiqueDeConfidentialitéController',
        ]);
    }
}
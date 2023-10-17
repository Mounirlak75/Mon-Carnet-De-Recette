<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
class HomeController extends AbstractController
{
    // Route pour afficher la page d'accueil (Page principale)
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Rendu de la vue index.html.twig pour la page d'accueil
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}

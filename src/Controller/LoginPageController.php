<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
class LoginPageController extends AbstractController
{
    // Route pour afficher la page de connexion
    #[Route('/login/page', name: 'app_login_page')]
    public function index(): Response
    {
        // Rendu de la vue index.html.twig pour la page de connexion
        return $this->render('login_page/index.html.twig', [
            'controller_name' => 'LoginPageController',
        ]);
    }
}

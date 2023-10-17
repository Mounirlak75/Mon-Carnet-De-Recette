<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/cgu')]
class CguController extends AbstractController
{
    // Route pour afficher la page des Conditions Générales d'Utilisation (CGU)
    #[Route('/', name: 'app_cgu')]
    public function index(): Response
    {
        // Rendu de la vue index.html.twig qui affiche le contenu des CGU
        return $this->render('cgu/index.html.twig', [
            'controller_name' => 'CguController',
        ]);
    }
}

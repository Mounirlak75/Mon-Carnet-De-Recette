<?php

namespace App\Controller;

use App\Repository\RecetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    // Définit une route pour cette action
    #[Route('/recherche', name: 'app_recherche')]
    public function index(Request $request, RecetteRepository $recetteRepository): Response
    {
        // Récupère le terme de recherche depuis la requête GET
        $recherche = $request->query->get('search');
        
        // Utilise le RecetteRepository pour rechercher les recettes correspondantes
        $recettes = $recetteRepository->findBySearch($recherche);
        
        // Rend la vue Twig 'recherche/index.html.twig' en passant les résultats de la recherche
        return $this->render('recherche/index.html.twig', [
            'controller_name' => 'RechercheController',
            'recettes' => $recettes
        ]);
    }
}

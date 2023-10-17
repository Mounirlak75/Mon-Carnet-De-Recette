<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\Categorie1Type;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/categorie')]
class CategorieController extends AbstractController
{
    // Route pour afficher la liste des catégories (Page d'index)
    #[Route('/', name: 'app_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        // Rendu de la vue index.html.twig avec la liste de toutes les catégories
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    // Route pour afficher les détails d'une catégorie
    #[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        // Récupération des recettes associées à la catégorie
        $recettes = $categorie->getRecettes();
        
        // Rendu de la vue show.html.twig avec les détails de la catégorie et ses recettes
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
            'recettes' => $recettes
        ]);
    }
}

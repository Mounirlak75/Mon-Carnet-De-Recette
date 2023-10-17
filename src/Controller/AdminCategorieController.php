<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/admin/categorie')]
class AdminCategorieController extends AbstractController
{
    // Route pour afficher la liste des catégories (Page d'index)
    #[Route('/', name: 'app_admin_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        // Rendu de la vue index.html.twig avec la liste de toutes les catégories
        return $this->render('admin_categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    // Route pour créer une nouvelle catégorie
    #[Route('/new', name: 'app_admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance de Categorie
        $categorie = new Categorie();
        
        // Création du formulaire associé à l'entité Categorie
        $form = $this->createForm(CategorieType::class, $categorie);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Persistance de la nouvelle catégorie en base de données
            $entityManager->persist($categorie);
            $entityManager->flush();

            // Redirection vers la page d'index après la création de la catégorie
            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu de la vue new.html.twig avec le formulaire de création de catégorie
        return $this->render('admin_categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    // Route pour afficher les détails d'une catégorie
    #[Route('/{id}', name: 'app_admin_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        // Rendu de la vue show.html.twig avec les détails de la catégorie
        return $this->render('admin_categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    // Route pour éditer une catégorie existante
    #[Route('/{id}/edit', name: 'app_admin_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire d'édition associé à l'entité Categorie
        $form = $this->createForm(CategorieType::class, $categorie);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour de la catégorie en base de données
            $entityManager->flush();

            // Redirection vers la page d'index après l'édition de la catégorie
            return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu de la vue edit.html.twig avec le formulaire d'édition de catégorie
        return $this->render('admin_categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    // Route pour supprimer une catégorie
    #[Route('/{id}', name: 'app_admin_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        // Vérification de la validité du jeton CSRF (Cross-Site Request Forgery)
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            // Suppression de la catégorie en base de données
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        // Redirection vers la page d'index après la suppression de la catégorie
        return $this->redirectToRoute('app_admin_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}

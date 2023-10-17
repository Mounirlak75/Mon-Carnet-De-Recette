<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/admin/recette')]
class AdminRecetteController extends AbstractController
{
    // Route pour afficher la liste des recettes (Page d'index)
    #[Route('/', name: 'app_admin_recette_index', methods: ['GET'])]
    public function index(RecetteRepository $recetteRepository): Response
    {
        // Rendu de la vue index.html.twig avec la liste de toutes les recettes
        return $this->render('admin_recette/index.html.twig', [
            'recettes' => $recetteRepository->findAll(),
        ]);
    }

    // Route pour créer une nouvelle recette
    #[Route('/new', name: 'app_admin_recette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance de Recette
        $recette = new Recette();
        
        // Création du formulaire associé à l'entité Recette
        $form = $this->createForm(RecetteType::class, $recette);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout de l'image de la recette (si vous déplacez cette ligne)
            $recette->setImageFile($form['imageFile']->getData());
            
            // Persistance de la nouvelle recette en base de données
            $entityManager->persist($recette);
            $entityManager->flush();

            // Flash message pour indiquer le succès de l'opération
            $this->addFlash('success', 'Recette ajoutée avec succès!');

            // Redirection vers la page d'index après la création de la recette
            return $this->redirectToRoute('app_admin_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu de la vue new.html.twig avec le formulaire de création de recette
        return $this->render('admin_recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    // Route pour afficher les détails d'une recette
    #[Route('/{id}', name: 'app_admin_recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        // Rendu de la vue show.html.twig avec les détails de la recette
        return $this->render('admin_recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    // Route pour éditer une recette existante
    #[Route('/{id}/edit', name: 'app_admin_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire d'édition associé à l'entité Recette
        $form = $this->createForm(RecetteType::class, $recette);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour de la recette en base de données
            $entityManager->flush();

            // Flash message pour indiquer le succès de l'opération
            $this->addFlash('success', 'Recette modifiée avec succès!');

            // Redirection vers la page d'index après l'édition de la recette
            return $this->redirectToRoute('app_admin_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu de la vue edit.html.twig avec le formulaire d'édition de recette
        return $this->render('admin_recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    // Route pour supprimer une recette
    #[Route('/{id}', name: 'app_admin_recette_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        // Vérification de la validité du jeton CSRF (Cross-Site Request Forgery)
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            // Suppression de la recette en base de données
            $entityManager->remove($recette);
            $entityManager->flush();

            // Flash message pour indiquer le succès de l'opération
            $this->addFlash('success', 'Recette supprimée avec succès!');
        }

        // Redirection vers la page d'index après la suppression de la recette
        return $this->redirectToRoute('app_admin_recette_index', [], Response::HTTP_SEE_OTHER);
    }
}

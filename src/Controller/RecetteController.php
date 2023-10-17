<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\Recette1Type;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/recette')]
class RecetteController extends AbstractController
{
    // Action pour afficher les recettes de l'utilisateur connecté
    #[Route("/mes-recettes", name: 'mes_recettes', methods: ['GET'])]
    public function mesRecettes(RecetteRepository $recetteRepository): Response
    {
        // Récupération de l'utilisateur actuel
        $user = $this->getUser();

        if (!$user) {
            // Redirection vers la page d'inscription ou affichage d'un message d'erreur
            return $this->redirectToRoute('app_register');
        }

        // Récupération des recettes de l'utilisateur
        $mesRecettes = $recetteRepository->findBy(['user' => $user]);

        return $this->render('recette/mes_recettes.html.twig', [
            'recettes' => $mesRecettes
        ]);
    }

    // Action pour afficher toutes les recettes
    #[Route('/', name: 'app_recette_index', methods: ['GET'])]
    public function index(RecetteRepository $recetteRepository): Response
    {
        // Comptage du nombre total de recettes
        $nombreRecettes = $recetteRepository->count([]);

        return $this->render('recette/index.html.twig', [
            'nombreRecettes' => $nombreRecettes,
            'recettes' => $recetteRepository->findAll(),
        ]);
    }

    // Action pour créer une nouvelle recette
    #[Route('/new', name: 'app_recette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance de Recette
        $recette = new Recette();
        $form = $this->createForm(Recette1Type::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définition du fichier d'image de la recette
            $recette->setImageFile($form['imageFile']->getData());
            
            // Enregistrement de la recette en base de données
            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    // Action pour afficher une recette
    #[Route('/{id}', name: 'app_recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        // Récupération des commentaires de la recette
        $commentaires = $recette->getCommentaires();
        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
            'commentaires' => $commentaires
        ]);
    }

    // Action pour éditer une recette
    #[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        // Vérifiez que l'utilisateur est le propriétaire de la recette
        if ($recette->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(Recette1Type::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définition du fichier d'image de la recette
            $recette->setImageFile($form['imageFile']->getData());
            
            // Enregistrement des modifications en base de données
            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    // Action pour supprimer une recette
    #[Route('/{id}', name: 'app_recette_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        // Vérification si l'utilisateur actuel est le propriétaire de la recette
        if ($recette->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
    
        // Vérification si le jeton CSRF (Cross-Site Request Forgery) est valide
        // Le jeton CSRF est généralement utilisé pour protéger les actions sensibles
        // contre les attaques de type CSRF.
        if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->request->get('_token'))) {
            // Si le jeton CSRF est valide, supprimez la recette de la base de données
            $entityManager->remove($recette);
            $entityManager->flush();
        }
    
        // Redirection vers la liste des recettes après la suppression
        return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
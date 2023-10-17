<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Recette;
use App\Form\Commentaire1Type;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/recette/{id}/commentaire')]
class CommentaireController extends AbstractController
{
    // Route pour créer un nouveau commentaire pour une recette
    #[Route('/new', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance de Commentaire
        $commentaire = new Commentaire();
        
        // Définition de la date du commentaire (utilisation de DateTimeImmutable)
        $commentaire->setDate(new DateTimeImmutable());
        
        // Définition de l'utilisateur actuel comme auteur du commentaire
        $commentaire->setUser($this->getUser());
        
        // Définition de la recette associée au commentaire
        $commentaire->setRecette($recette);
        
        // Création du formulaire associé à l'entité Commentaire
        $form = $this->createForm(Commentaire1Type::class, $commentaire);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Persistance du nouveau commentaire en base de données
            $entityManager->persist($commentaire);
            $entityManager->flush();

            // Redirection vers la page de la recette après la création du commentaire
            return $this->redirectToRoute('app_recette_show', [
                'id' => $recette->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        // Rendu de la vue new.html.twig avec le formulaire de création de commentaire
        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }
}

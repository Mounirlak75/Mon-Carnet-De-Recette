<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/admin/commentaire')]
class AdminCommentaireController extends AbstractController
{
    // Route pour afficher la liste des commentaires (Page d'index)
    #[Route('/', name: 'app_admin_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        // Rendu de la vue index.html.twig avec la liste de tous les commentaires
        return $this->render('admin_commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }

    // Route pour afficher les détails d'un commentaire
    #[Route('/{id}', name: 'app_admin_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        // Rendu de la vue show.html.twig avec les détails du commentaire
        return $this->render('admin_commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    // Route pour supprimer un commentaire
    #[Route('/{id}', name: 'app_admin_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        // Vérification de la validité du jeton CSRF (Cross-Site Request Forgery)
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            // Suppression du commentaire en base de données
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        // Redirection vers la page d'index après la suppression du commentaire
        return $this->redirectToRoute('app_admin_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }
}

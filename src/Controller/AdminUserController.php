<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    // Route pour afficher la liste des utilisateurs (Page d'index)
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // Rendu de la vue index.html.twig avec la liste de tous les utilisateurs
        return $this->render('admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    // Route pour créer un nouvel utilisateur
    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Création d'une nouvelle instance de User
        $user = new User();
        
        // Création du formulaire associé à l'entité User
        $form = $this->createForm(UserType::class, $user);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du mot de passe en clair depuis le formulaire
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Hachage du mot de passe
            $hashPassword = $passwordHasher->hashPassword($user, $plainPassword);
            
            // Définition du mot de passe haché dans l'entité User
            $user->setPassword($hashPassword);
            
            // Persistance du nouvel utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection vers la page d'index après la création de l'utilisateur
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu de la vue new.html.twig avec le formulaire de création d'utilisateur
        return $this->render('admin_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    // Route pour afficher les détails d'un utilisateur
    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // Rendu de la vue show.html.twig avec les détails de l'utilisateur
        return $this->render('admin_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    // Route pour éditer un utilisateur existant
    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Création du formulaire d'édition associé à l'entité User
        $form = $this->createForm(UserType::class, $user);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du mot de passe en clair depuis le formulaire
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Hachage du mot de passe
            $hashPassword = $passwordHasher->hashPassword($user, $plainPassword);
            
            // Définition du mot de passe haché dans l'entité User
            $user->setPassword($hashPassword);
            
            // Mise à jour de l'utilisateur en base de données
            $entityManager->flush();

            // Redirection vers la page d'index après l'édition de l'utilisateur
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu de la vue edit.html.twig avec le formulaire d'édition d'utilisateur
        return $this->render('admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    // Route pour supprimer un utilisateur
    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérification de la validité du jeton CSRF (Cross-Site Request Forgery)
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Suppression de l'utilisateur en base de données
            $entityManager->remove($user);
            $entityManager->flush();
        }

        // Redirection vers la page d'index après la suppression de l'utilisateur
        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
    
}

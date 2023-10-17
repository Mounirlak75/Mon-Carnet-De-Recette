<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
#[Route('/profile')]
class ProfileController extends AbstractController
{
    // Route pour afficher le profil de l'utilisateur
    #[Route('', name: 'app_profile_show', methods: ['GET'])]
    public function show(): Response
    {
        // Récupération de l'utilisateur actuel
        $user = $this->getUser();
    
        // Rendu de la vue show.html.twig avec les détails du profil de l'utilisateur
        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    // Route pour éditer le profil de l'utilisateur
    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupération de l'utilisateur actuel
        $user = $this->getUser();
        
        // Création du formulaire de modification de profil en utilisant User1Type (à définir dans votre code)
        $form = $this->createForm(User1Type::class, $user);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);

        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du mot de passe en texte brut
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Hachage du nouveau mot de passe
            $hashPassword = $passwordHasher->hashPassword($user, $plainPassword);
            
            // Mise à jour du mot de passe de l'utilisateur
            $user->setPassword($hashPassword);
            
            // Enregistrement des modifications en base de données
            $entityManager->flush();

            // Redirection vers la page de profil après la modification
            return $this->redirectToRoute('app_profile_show', [], Response::HTTP_SEE_OTHER);
        }

        // Rendu de la vue edit.html.twig avec le formulaire de modification de profil
        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}

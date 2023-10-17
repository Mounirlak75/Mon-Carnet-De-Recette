<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

// Définition de la classe du contrôleur
class ContactController extends AbstractController
{
    // Route pour afficher le formulaire de contact
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Création du formulaire de contact en utilisant ContactType (à définir dans votre code)
        $form = $this->createForm(ContactType::class);
        
        // Traitement du formulaire lors de sa soumission
        $form->handleRequest($request);
        
        // Vérification de la soumission du formulaire et de sa validité
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $adresse = $form->get('email')->getData();
            $sujet = $form->get('sujet')->getData();
            $contenu = $form->get('contenu')->getData();
            
            // Création d'un objet Email pour envoyer le message
            $email = (new Email())
                ->from($adresse)
                ->to('you@example.com') // Remplacez par l'adresse email de destination
                ->subject($sujet)
                ->text($contenu);
            
            // Envoi de l'email à l'adresse de destination
            $mailer->send($email);
            
            // Redirection vers une page de succès après l'envoi du message
            return $this->redirectToRoute('app_succes');
        }
        
        // Rendu de la vue index.html.twig avec le formulaire de contact
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form->createView()
        ]);
    }

    // Route pour afficher une page de succès après l'envoi du message
    #[Route('/contact/succes', name: 'app_succes')]
    public function succes(): Response
    {
        return $this->render('succes/index.html.twig', [
            'controller_name' => 'SuccesController',
        ]);
    }
}

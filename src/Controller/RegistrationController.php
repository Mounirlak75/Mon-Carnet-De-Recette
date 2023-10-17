<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController

//La méthode verifyUserEmail est définie pour gérer la vérification de l'e-mail de l'utilisateur. Elle vérifie que l'utilisateur est authentifié, puis valide le lien de confirmation d'e-mail en utilisant le service EmailVerifier. Si la validation échoue, l'utilisateur est redirigé vers la page d'enregistrement avec un message d'erreur. Si la validation réussit, l'utilisateur est redirigé vers une page de succès avec un message de succès.
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    // Action pour l'enregistrement d'un nouvel utilisateur
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Crée une nouvelle instance de l'entité User
        $user = new User();
        
        // Crée un formulaire d'enregistrement en utilisant RegistrationFormType
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode le mot de passe en utilisant le service UserPasswordHasherInterface
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Enregistre l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoie un e-mail de confirmation à l'utilisateur
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@mcdr.com', 'No Reply'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // Redirige l'utilisateur vers la page d'accueil après l'enregistrement
            return $this->redirectToRoute('app_home');
        }

        // Rend la vue Twig 'registration/register.html.twig' en passant le formulaire d'enregistrement
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    // Action pour vérifier l'e-mail de l'utilisateur
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // Vérifie que l'utilisateur est authentifié
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Valide le lien de confirmation d'e-mail, définit User::isVerified=true et persiste
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // Redirige l'utilisateur vers une page de succès après la vérification de l'e-mail
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}

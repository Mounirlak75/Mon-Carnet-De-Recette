<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Affiche et traite le formulaire pour demander une réinitialisation du mot de passe.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        // Crée le formulaire de demande de réinitialisation de mot de passe
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, traite l'envoi de l'e-mail de réinitialisation
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer,
                $translator
            );
        }

        // Rend la vue Twig 'reset_password/request.html.twig' avec le formulaire
        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Page de confirmation après que l'utilisateur ait demandé une réinitialisation du mot de passe.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Génère un faux jeton si l'utilisateur n'existe pas ou si quelqu'un a accédé à cette page directement.
        // Cela évite de révéler si un utilisateur a été trouvé avec l'adresse e-mail donnée ou non
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        // Rend la vue Twig 'reset_password/check_email.html.twig' avec le jeton de réinitialisation
        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Valide et traite l'URL de réinitialisation que l'utilisateur a cliqué dans son e-mail.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, string $token = null): Response
    {
        if ($token) {
            // Si un token est passé en tant que paramètre, il est stocké en session et l'utilisateur est redirigé pour éviter
            // que l'URL avec le token ne soit exposée à des tiers.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        // Récupère le token de réinitialisation de la session
        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('Aucun jeton de réinitialisation du mot de passe trouvé dans l\'URL ou dans la session.');
        }

        try {
            // Valide le token et récupère l'utilisateur associé
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            // En cas d'erreur de validation du token, redirige l'utilisateur vers la demande de réinitialisation du mot de passe
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Le token est valide, permet à l'utilisateur de changer son mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Supprime la demande de réinitialisation du mot de passe une fois le mot de passe changé
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode (hache) le nouveau mot de passe et l'assigne à l'utilisateur
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // Nettoie la session après la réinitialisation du mot de passe
            $this->cleanSessionAfterReset();

            // Redirige l'utilisateur vers la page d'accueil après la réinitialisation
            return $this->redirectToRoute('app_home');
        }

        // Rend la vue Twig 'reset_password/reset.html.twig' avec le formulaire de réinitialisation
        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    // Méthode privée pour gérer l'envoi de l'e-mail de réinitialisation du mot de passe
    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, TranslatorInterface $translator): RedirectResponse
    {
        // Recherche l'utilisateur en utilisant l'adresse e-mail
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Ne révèle pas si un compte utilisateur a été trouvé ou non
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            // Génère un token de réinitialisation et l'envoie par e-mail
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // En cas d'erreur lors de la génération du token, redirige l'utilisateur vers la page de vérification de l'e-mail
            // Vous pouvez décommenter les lignes ci-dessous si vous souhaitez afficher un message d'erreur spécifique
            // Vous devez être prudent, car cela peut révéler si un utilisateur est enregistré ou non
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
            //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            // ));

            return $this->redirectToRoute('app_check_email');
        }

        // Crée et envoie l'e-mail de réinitialisation
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@mcdr.com', 'No Reply'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);

        // Stocke le token en session pour la récupération dans la route 'app_check_email'
        $this->setTokenObjectInSession($resetToken);

        // Redirige l'utilisateur vers la page de vérification de l'e-mail
        return $this->redirectToRoute('app_check_email');
    }
}

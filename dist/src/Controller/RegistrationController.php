<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier) {}

    #[Route('/register', name: 'app_register')]
public function register(
    Request $request,
    UserPasswordHasherInterface $hasher,
    Security $security,
    EntityManagerInterface $em
): Response {
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && !$form->isValid()) {
        // Debug optionnel
        // dd((string) $form->getErrors(true, false));
    }

    if ($form->isSubmitted() && $form->isValid()) {
        $plain = (string) $form->get('plainPassword')->getData();
        $user->setPassword($hasher->hashPassword($user, $plain));

        // ✅ Enregistre le nouvel utilisateur
        $em->persist($user);
        $em->flush();

        // ✅ Envoi de l’e-mail de confirmation
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@knowledge.test', 'Knowledge'))
            ->to($user->getEmail())
            ->subject('Confirme ton adresse email')
            ->htmlTemplate('registration/confirmation_email.html.twig')
            ->context(['user' => $user]); // ton template l'utilise

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, $email);

        // ✅ Message de confirmation
        $this->addFlash('success', 'Un email de confirmation vient de t’être envoyé.');

        return $this->redirectToRoute('app_home');
    }

   


        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        TranslatorInterface $translator,
        UserRepository $users,
        EmailVerifier $emailVerifier,
        EntityManagerInterface $em
    ): Response {
        $id = $request->query->get('id');
        if (!$id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $users->find($id);
        if (!$user) {
            return $this->redirectToRoute('app_register');
        }

        try {
            $emailVerifier->handleEmailConfirmation($request, $user);
            $em->flush(); // <= ENREGISTRE isVerified=true
            $this->addFlash('success', 'Adresse email vérifiée avec succès.');
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $translator->trans($e->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        // (optionnel) auto-login après vérif :
        // return $this->container->get(Security::class)->login($user, LoginFormAuthenticator::class, 'main');

        return $this->redirectToRoute('app_home');
    }
}

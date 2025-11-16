<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer
    ) {}

    public function sendEmailConfirmation(string $verifyRouteName, User $user, Email $email): void
    {
        $sig = $this->verifyEmailHelper->generateSignature(
            $verifyRouteName,
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        $ctx = $email->getContext();
        $ctx['signedUrl'] = $sig->getSignedUrl();
        $ctx['expiresAtMessageKey'] = $sig->getExpirationMessageKey();
        $ctx['expiresAtMessageData'] = $sig->getExpirationMessageData();
        $ctx['user'] = $ctx ['user'] ?? $user; // utile si ton template l’utilise

        $email->context($ctx);

        $this->mailer->send($email);
    }

    public function handleEmailConfirmation($request, User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation(
            $request->getUri(),
            $user->getId(),
            $user->getEmail()
        );

        $user->setIsVerified(true);
    }
}

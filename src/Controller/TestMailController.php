<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;

class TestMailController extends AbstractController
{
    #[Route('/test-mail', name: 'test_mail')]
    public function test(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('no-reply@knowledge.test')
            ->to('test@example.com') // juste pour le test, ce sera visible dans Mailpit
            ->subject('Test Mailpit')
            ->text('Si tu vois ce mail dans Mailpit, le mailer est OK ✅');

        $mailer->send($email);

        return new Response('Mail de test envoyé (si la config est bonne).');
    }
}

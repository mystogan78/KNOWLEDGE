<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Repository\UserRepository;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StripeWebhookController extends AbstractController
{
    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $users,
        CourseRepository $courses,
        LessonRepository $lessons
    ): Response {
        $payload = $request->getContent();
        $sig = $request->headers->get('stripe-signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;

        if (!$secret) {
            return new Response('Missing STRIPE_WEBHOOK_SECRET', 500);
        }

        // ✅ Vérification de la signature Stripe
        try {
            $event = Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            return new Response('Invalid signature', 400);
        }

        // 🎯 On s'intéresse uniquement à l'événement de paiement réussi
        if ($event->type === 'checkout.session.completed') {
            /** @var \Stripe\Checkout\Session $session */
            $session = $event->data->object;

            // Metadata posées au moment de la création de la session
            $type = $session->metadata->type ?? null;
            $courseId = isset($session->metadata->courseId) ? (int) $session->metadata->courseId : null;
            $lessonId = isset($session->metadata->lessonId) ? (int) $session->metadata->lessonId : null;
            $userId = isset($session->metadata->userId) ? (int) $session->metadata->userId : null;

            // Vérifie si l'utilisateur existe
            $user = $userId ? $users->find($userId) : null;
            if (!$user) {
                return new Response('User not found', 200);
            }

            // Vérifie si on a déjà enregistré ce paiement (idempotence)
            $existing = $em->getRepository(Purchase::class)->findOneBy([
                'providerSessionId' => $session->id
            ]);
            if ($existing) {
                return new Response('Already processed', 200);
            }

            // Montant du paiement (en euros)
            $amount = ((float) $session->amount_total) / 100;

            // ✅ Crée un nouvel achat
            $purchase = new Purchase();
            $purchase
                ->setUser($user)
                ->setAmount(number_format($amount, 2, '.', ''))
                ->setCurrency($session->currency ?? 'eur')
                ->setStatus('paid')
                ->setProvider('stripe')
                ->setProviderSessionId($session->id)
                ->setProviderPaymentIntentId($session->payment_intent ?? null)
                ->setCreatedAt(new \DateTimeImmutable());

            // Si achat de cours
            if ($type === 'course' && $courseId) {
                $course = $courses->find($courseId);
                if ($course) {
                    $purchase->setCourse($course);
                }
            }

            // Si achat de leçon
            if ($type === 'lesson' && $lessonId) {
                $lesson = $lessons->find($lessonId);
                if ($lesson) {
                    $purchase->setLesson($lesson);
                    $purchase->setCourse($lesson->getCourse());
                }
            }

            $em->persist($purchase);
            $em->flush();
        }

        return new Response('ok', 200);
    }
}

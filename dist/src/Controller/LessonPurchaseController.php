<?php
namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\LessonRepository;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LessonPurchaseController extends AbstractController
{
   #[Route('/achat/lecon/{id}', name: 'purchase_lesson', methods: ['POST','GET'])]
public function start(int $id, LessonRepository $lessons): RedirectResponse
{
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    $lesson = $lessons->find($id);
    if (!$lesson) {
        throw $this->createNotFoundException('Leçon introuvable');
    }

    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

    $amountCents = (int) round((float) $lesson->getPrice() * 100);

    $successUrl = $this->generateUrl('purchase_success', [], UrlGeneratorInterface::ABSOLUTE_URL) . '?s={CHECKOUT_SESSION_ID}';
    $cancelUrl  = $this->generateUrl('purchase_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

    $customerEmail = $this->getUser()->getUserIdentifier();

    $session = CheckoutSession::create([
        'mode' => 'payment',
        'client_reference_id' => (string) $this->getUser()->getId(),
        'customer_email' => $customerEmail,
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $amountCents,
                'product_data' => [
                    'name' => 'Leçon — ' . $lesson->getTitle(),
                ],
            ],
            'quantity' => 1,
        ]],
        'metadata' => [
            'type'     => 'lesson',
            'lessonId' => (string) $lesson->getId(),
            'courseId' => (string) $lesson->getCourse()->getId(),
            'userId'   => (string) $this->getUser()->getId(),
        ],
        'allow_promotion_codes' => true,
        'success_url' => $successUrl,
        'cancel_url' => $cancelUrl,
    ]);

    return new RedirectResponse($session->url);
}
}
<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LessonPurchaseController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/achat/lecon/{id}', name: 'purchase_lesson', methods: ['POST', 'GET'])]
    public function start(int $id, LessonRepository $lessons): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $lesson = $lessons->find($id);
        if (!$lesson) {
            throw $this->createNotFoundException('Leçon introuvable');
        }

        // ✅ Configuration Stripe
        if (empty($_ENV['STRIPE_SECRET_KEY'])) {
            throw new \RuntimeException('Clé Stripe manquante dans le fichier .env.local');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $amountCents = (int) round((float) $lesson->getPrice() * 100);
        $customerEmail = $this->getUser()->getUserIdentifier();

        // ✅ URLs Stripe
        $successUrl = str_replace(
            ['%7B', '%7D'],
            ['{', '}'],
            $this->generateUrl('purchase_success', [
                'sessionId' => '{CHECKOUT_SESSION_ID}'
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $cancelUrl = $this->generateUrl('purchase_cancel', [
            'sessionId' => '{CHECKOUT_SESSION_ID}'
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // ✅ Création de la session Stripe Checkout
        try {
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
        } catch (\Exception $e) {
            // 🚨 En cas d'erreur Stripe
            $this->addFlash('danger', 'Erreur Stripe : ' . $e->getMessage());
            return $this->redirectToRoute('lesson_show', ['id' => $lesson->getId()]);
        }

        // ✅ Vérification que Stripe renvoie bien une session ID
        if (empty($session->id)) {
            throw new \RuntimeException('Erreur Stripe : aucune session ID reçue.');
        }

     // ✅ Enregistrement de l'achat
$purchase = new Purchase();
$purchase->setUser($this->getUser());
$purchase->setLesson($lesson);
$purchase->setAmount($lesson->getPrice());
$purchase->setCurrency('eur');
$purchase->setStatus('pending');
$purchase->setProvider('stripe');
$purchase->setProviderSessionId($session->id);
$purchase->setCreatedAt(new \DateTimeImmutable()); // 🕓 Ajout de la date de création

$this->em->persist($purchase);
$this->em->flush();


        // ✅ Redirection vers Stripe
        return new RedirectResponse($session->url);
    }
}

<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Repository\CourseRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PurchaseController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UrlGeneratorInterface $urls,
        private readonly PurchaseRepository $purchases,
        private readonly CourseRepository $courses,
        private readonly StripeClient $stripe, // service Stripe\StripeClient (autowire via STRIPE_SECRET_KEY)
    ) {}

    #[Route('/achat/{slug}', name: 'purchase_start', methods: ['GET','POST'])]
    public function start(string $slug): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $course = $this->courses->findOneBy(['slug' => $slug]);
        if (!$course) {
            throw $this->createNotFoundException('Cursus introuvable');
        }

        // Déjà acheté ?
        $already = $this->purchases->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.user = :u')->andWhere('p.course = :c')->andWhere('p.status = :st')
            ->setParameters(['u' => $this->getUser(), 'c' => $course, 'st' => 'paid'])
            ->getQuery()->getSingleScalarResult() > 0;

        if ($already) {
            $this->addFlash('info', 'Vous avez déjà acheté ce cursus.');
            return $this->redirectToRoute('course_show', ['slug' => $course->getSlug()]);
        }

        // 1) Créer la commande en "pending"
        $purchase = (new Purchase())
            ->setCourse($course)
            ->setUser($this->getUser())
            ->setAmount($course->getPrice())
            ->setCurrency('eur')
            ->setStatus('pending')
            ->setProvider('stripe');

        $this->em->persist($purchase);
        $this->em->flush();

        // 2) URLs de retour
        $successUrl = $this->urls->generate('purchase_success', [
            'sessionId' => '{CHECKOUT_SESSION_ID}',
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $cancelUrl = $this->urls->generate('purchase_cancel', [
            'sessionId' => '{CHECKOUT_SESSION_ID}',
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // 3) Créer la session de paiement Stripe
        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) round($course->getPrice() * 100), // en centimes
                    'product_data' => [
                        'name' => $course->getTitle(),
                        'metadata' => [
                            'course_id' => (string) $course->getId(),
                            'course_slug' => $course->getSlug(),
                        ],
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'purchase_id' => (string) $purchase->getId(),
            ],
        ]);

        // 4) Sauver l’id de session Stripe
        $purchase->setProviderSessionId($session->id);
        $this->em->flush();

        // 5) Rediriger vers Stripe
        return $this->redirect($session->url);
    }

    #[Route('/achat/success/{sessionId}', name: 'purchase_success', methods: ['GET'])]
    public function success(string $sessionId): Response
    {
        // Récupérer la session pour vérifier le statut
        $session = $this->stripe->checkout->sessions->retrieve($sessionId, []);
        $purchase = $this->purchases->findOneBy(['providerSessionId' => $sessionId]);

        if (!$purchase) {
            throw $this->createNotFoundException('Achat introuvable');
        }

        if ($session->payment_status === 'paid' && $purchase->getStatus() !== 'paid') {
            $purchase->setStatus('paid');
            $this->em->flush();
        }

        return $this->render('purchase/success.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    #[Route('/achat/cancel/{sessionId}', name: 'purchase_cancel', methods: ['GET'])]
    public function cancel(string $sessionId = null): Response
    {
        $this->addFlash('warning', 'Le paiement a été annulé.');
        return $this->render('purchase/cancel.html.twig');
    }
}

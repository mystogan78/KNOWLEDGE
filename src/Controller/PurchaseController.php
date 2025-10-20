<?php
namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PurchaseController extends AbstractController
{
    #[Route('/achat/{id}', name: 'purchase_start')]
    public function start(int $id, CourseRepository $courses): Response
    {
        $course = $courses->find($id);
        if (!$course) {
            throw $this->createNotFoundException('Cours introuvable');
        }

        // Ici tu brancheras Stripe/PayPal plus tard
        return $this->render('purchase/confirm.html.twig', [
            'course' => $course,
        ]);
    }
}

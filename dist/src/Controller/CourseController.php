<?php
namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Repository\PurchaseRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/course')]
final class CourseController extends AbstractController
{
    #[Route('', name: 'course_index', methods: ['GET'])]
    public function index(CourseRepository $courses): Response
    {
        // Utilise ta méthode perso si elle existe, sinon fallback
        $all = method_exists($courses, 'findAllOrderedByTitle')
            ? $courses->findAllOrderedByTitle()
            : $courses->findBy([], ['title' => 'ASC']);

        return $this->render('course/index.html.twig', [
            'courses' => $all,
        ]);
    }

    #[Route('/{slug}', name: 'course_show', methods: ['GET'])]
    public function show(
        #[MapEntity(expr: 'repository.findOneBy({slug: slug})')] Course $course,
        PurchaseRepository $purchases
    ): Response {
        $isPurchased = false;

        if ($this->getUser()) {
            $isPurchased = $purchases->findOneBy([
                'user'   => $this->getUser(),
                'course' => $course,
                'status' => 'paid',
            ]) !== null;
        }

        return $this->render('course/show.html.twig', [
            'course'       => $course,
            'isPurchased'  => $isPurchased,
        ]);
    }
}

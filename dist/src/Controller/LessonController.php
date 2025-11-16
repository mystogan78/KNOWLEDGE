<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\LessonRepository;
use App\Repository\PurchaseRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lesson')]
final class LessonController extends AbstractController
{
    #[Route('', name: 'lesson_index', methods: ['GET'])]
    public function index(LessonRepository $lessons): Response
    {
        // Récupère toutes les leçons, triées par position ou titre
        $all = $lessons->findBy([], ['position' => 'ASC', 'title' => 'ASC']);

        return $this->render('lesson/index.html.twig', [
            'lessons' => $all,
        ]);
    }

    #[Route('/{slug}', name: 'lesson_show', methods: ['GET'])]
    public function show(
        #[MapEntity(expr: 'repository.findOneBy({slug: slug})')] Lesson $lesson,
        LessonRepository $lessons,
        PurchaseRepository $purchases
    ): Response {
        $isPurchased = false;

        if ($this->getUser()) {
            // ✅ Vérifie si l'utilisateur a acheté la leçon
            $hasLesson = $purchases->findOneBy([
                'user'   => $this->getUser(),
                'lesson' => $lesson,
                'status' => 'paid',
            ]);

            // ✅ Vérifie aussi si l'utilisateur a acheté le cursus complet
            $hasCourse = $purchases->findOneBy([
                'user'   => $this->getUser(),
                'course' => $lesson->getCourse(),
                'status' => 'paid',
            ]);

            // ✅ Si l’un ou l’autre est vrai, la leçon est accessible
            $isPurchased = ($hasLesson !== null) || ($hasCourse !== null);
        }

        // Si tu veux gérer le contrôle d'accès via voter :
        // if (!$isPurchased) {
        //     $this->denyAccessUnlessGranted('COURSE_VIEW', $lesson->getCourse());
        // }

        // Navigation entre les leçons (si tu veux ajouter “suivante / précédente” plus tard)
        $prev = method_exists($lessons, 'findPrev') ? $lessons->findPrev($lesson) : null;
        $next = method_exists($lessons, 'findNext') ? $lessons->findNext($lesson) : null;

        return $this->render('lesson/show.html.twig', [
            'lesson'      => $lesson,
            'isPurchased' => $isPurchased,
            'prev'        => $prev,
            'next'        => $next,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_index')]
    public function index(CategoryRepository $categories): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categories->findAllOrderedByName(),
        ]);
    }

    #[Route('/category/{slug}', name: 'category_show')]
    public function show(
        string $slug,
        CategoryRepository $categories,
        CourseRepository $courses
    ): Response {
        $category = $categories->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable');
        }

        // Tous les cours de cette catégorie (triés par titre)
        $categoryCourses = $courses->createQueryBuilder('c')
            ->andWhere('c.category = :cat')->setParameter('cat', $category)
            ->orderBy('c.title', 'ASC')
            ->getQuery()->getResult();

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'courses'  => $categoryCourses,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/course')]
class CourseController extends AbstractController
{
    #[Route('/new', name: 'course_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $course = new Course();

        // Création du formulaire lié à l'entité
        $form = $this->createForm(CourseType::class, $course);

        // Gère la soumission (POST)
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($course);
            $em->flush();

            // Redirection après création
            return $this->redirectToRoute('category_show', [
                'categorieSlug' => $course->getCategory()->getSlug(),
            ]);
        }

        // Affiche le formulaire
        return $this->render('course/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

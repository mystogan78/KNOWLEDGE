<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Helpers
        $createCourse = function (Category $cat, string $title, string $slug, string $price, ?string $desc) use ($manager): Course {
            $c = new Course();
            $c->setCategory($cat);
            $c->setTitle($title);
            $c->setSlug($slug);
            $c->setPrice($price);
            $c->setDescription($desc);
            $manager->persist($c);
            return $c;
        };

        $createLesson = function (Course $course, string $title, string $slug, string $price, ?string $content, int $position) use ($manager): Lesson {
            $l = new Lesson();
            $l->setCourse($course);
            $l->setTitle($title);
            $l->setSlug($slug);
            $l->setPrice($price);
            $l->setContent($content);
            $l->setPosition($position);
            $manager->persist($l);
            return $l;
        };

        // ---------------------------- Informatique ------------------------------------
        $informatique = new Category();
        $informatique->setName('Informatique');
        $informatique->setSlug('informatique');
        $manager->persist($informatique);

        // Cursus Web
        $web = $createCourse(
            $informatique,
            "Cursus d'initiation au developpement web",
            'cursus-initiation-developpement-web',
            "60.00",
            "Parcours complet d'initiation : HTML/CSS + JS."
        );
        // Leçons du cursus Web
        $createLesson(
            $web,
            "Leçon n°1 : Les langages HTML5 et CSS3",
            'lecon-1-langages-html5-css3',
            "32.00",
            "Créez vos premières pages web avec HTML5 et CSS3.",
            1
        );
        $createLesson(
            $web,
            "Leçon n°2 : Dynamiser votre site avec JavaScript",
            'lecon-2-langage-javascript',
            "32.00",
            "Apprenez les bases du langage JavaScript pour rendre vos pages web interactives.",
            2
        );

        // ---------------------------- Jardinage ------------------------------------
        $jardinage = new Category();
        $jardinage->setName('Jardinage');
        $jardinage->setSlug('jardinage');
        $manager->persist($jardinage);

        $jardin = $createCourse(
            $jardinage,
            "Cursus d'initiation au jardinage",
            'cursus-initiation-jardinage',
            "30.00",
            "Apprenez à créer et entretenir votre jardin."
        );
        $createLesson(
            $jardin,
            "Leçon n°1 : Les outils du jardinier",
            'lecon-1-outils-jardinier',
            "16.00",
            "Découvrez les outils indispensables pour bien débuter le jardinage.",
            1
        );
        $createLesson(
            $jardin,
            "Leçon n°2 : Jardiner avec la lune",
            'lecon-2-jardiner-avec-lune',
            "16.00",
            "Apprenez à jardiner en suivant les phases de la lune.",
            2
        );

        // ---------------------------- Cuisine ------------------------------------
        $cuisine = new Category();
        $cuisine->setName('Cuisine');
        $cuisine->setSlug('cuisine');
        $manager->persist($cuisine);

        $cuisineBase = $createCourse(
            $cuisine,
            "Cursus d'initiation à la cuisine",
            'cursus-initiation-cuisine',
            "40.00",
            "Apprenez les bases de la cuisine pour régaler vos proches."
        );
        $createLesson(
            $cuisineBase,
            "Leçon n°1 : Les modes de cuisson",
            'lecon-1-modes-cuisson',
            "23.00",
            "Maîtrisez les différentes techniques de cuisson des aliments.",
            1
        );
        $createLesson(
            $cuisineBase,
            "Leçon n°2 : Les saveurs",
            'lecon-2-saveurs',
            "23.00",
            "Apprenez à associer les saveurs pour créer des plats savoureux.",
            2
        );

        $dressage = $createCourse(
            $cuisine,
            "Cursus d'initiation à l'art du dressage culinaire",
            'cursus-initiation-art-dressage-culinaire',
            "48.00",
            "Apprenez à dresser vos assiettes comme un chef."
        );
        $createLesson(
            $dressage,
            "Leçon n°1 : Mettre en œuvre le style dans l'assiette",
            'lecon-1-mettre-en-oeuvre-style-assiette',
            "26.00",
            "Découvrez les principes de base du dressage culinaire.",
            1
        );
        $createLesson(
            $dressage,
            "Leçon n°2 : Harmoniser un repas à quatre plats",
            'lecon-2-harmoniser-repas-quatre-plats',
            "26.00",
            "Apprenez les techniques de dressage pour sublimer vos plats.",
            2
        );

        // ---------------------------- Musique ----------------------------------------------
        $musique = new Category();
        $musique->setName('Musique');
        $musique->setSlug('musique');
        $manager->persist($musique);

        $guitare = $createCourse(
            $musique,
            "Cursus d'initiation à la guitare",
            'cursus-initiation-guitare',
            "50.00",
            "Apprenez les bases de la guitare pour jouer vos morceaux préférés."
        );
        $createLesson(
            $guitare,
            "Leçon n°1 : Découverte de l'instrument",
            'lecon-1-decouverte-instrument',
            "28.00",
            "Apprenez les accords essentiels pour débuter à la guitare.",
            1
        );
        $createLesson(
            $guitare,
            "Leçon n°2 : Les accords et les gammes",
            'lecon-2-accords-gammes',
            "26.00",
            "Apprenez les accords essentiels et les gammes de base.",
            2
        );

        $piano = $createCourse(
            $musique,
            "Cursus d'initiation au piano",
            'cursus-initiation-piano',
            "50.00",
            "Apprenez les bases du piano pour jouer vos morceaux préférés."
        );
        $createLesson(
            $piano,
            "Leçon n°1 : Découverte de l'instrument",
            'lecon-1-decouverte-instrument-piano',
            "26.00",
            "Apprenez les bases du piano et découvrez l'instrument.",
            1
        );
        $createLesson(
            $piano,
            "Leçon n°2 : Les accords et les gammes",
            'lecon-2-accords-gammes-piano',
            "26.00",
            "Apprenez les accords et les gammes pour bien débuter le piano.",
            2
        );

        $manager->flush();
    }
}

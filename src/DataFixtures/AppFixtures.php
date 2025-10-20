<?php
namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $createCourse = function (Category $cat, string $title, string $slug, string $price, string $desc) use ($manager) {
            $c = new Course();
            $c->setCategory($cat);
            $c->setTitle($title);
            $c->setSlug($slug);
            $c->setPrice($price);
            $c->setDescription($desc);
            $manager->persist($c);
        };

        // ---------------------------- Informatique ------------------------------------

        $informatique = new Category();
        $informatique->setName('Informatique');
        $informatique->setSlug('informatique');
        $manager->persist($informatique);

        // Cursus + leçons
        $createCourse(
            $informatique,
            "Cursus d'initiation au developpement web",
            'cursus-initiation-developpement-web',
            "60.00",
            "Parcours complet d'initiation : HTML/CSS + JS."
        );
        $createCourse(
            $informatique,
            "Leçon n°1 : Les langages HTML5 et CSS3",
            'lecon-1-langages-html5-css3',
            "32.00",
            "Créez vos premières pages web avec HTML5 et CSS3."
        );
        $createCourse(
            $informatique,
            "Leçon n°2 : Dynamiser votre site avec JavaScript",
            'lecon-2-langage-javascript',
            "32.00",
            "Apprenez les bases du langage JavaScript pour rendre vos pages web interactives."
        );

        // ---------------------------- Jardinage ------------------------------------

        $jardinage = new Category();
        $jardinage->setName('Jardinage');
        $jardinage->setSlug('jardinage');
        $manager->persist($jardinage);

        $createCourse(
            $jardinage,
            "Cursus d'initiation au jardinage",
            'cursus-initiation-jardinage',
            "30.00",
            "Apprenez à créer et entretenir votre jardin."
        );
        $createCourse(
            $jardinage,
            "Leçon n°1 : Les outils du jardinier",
            'lecon-1-outils-jardinier',
            "16.00",
            "Découvrez les outils indispensables pour bien débuter le jardinage."
        );
        $createCourse(
            $jardinage,
            "leçon n°2 : Jardiner avec la lune",
            'lecon-2-jardiner-avec-lune',
            "16.00",
            "Apprenez à jardiner en suivant les phases de la lune."
        );

        // ---------------------------- Cuisine ------------------------------------
        $cuisine = new Category();
        $cuisine->setName('Cuisine');
        $cuisine->setSlug('cuisine');
        $manager->persist($cuisine);

        $createCourse(
            $cuisine,
            "Cursus d'initiation à la cuisine",
            'cursus-initiation-cuisine',
            "40.00",
            "Apprenez les bases de la cuisine pour régaler vos proches."
        );
        $createCourse(
            $cuisine,
            "Leçon n°1 : Les modes de cuisson",
            'lecon-1-modes-cuisson',
            "23.00",
            "Maîtrisez les différentes techniques de cuisson des aliments."
        );
        $createCourse(
            $cuisine,
            "Leçon n°2 : Les saveurs",
            'lecon-2-saveurs',
            "23.00",
            "Apprenez à associer les saveurs pour créer des plats savoureux."
        );

        $createCourse(
            $cuisine,
            "Cursus d'initiation à l'art du dressage culinaire",
            'cursus-initiation-art-dressage-culinaire',
            "48.00",
            "Apprenez à dresser vos assiettes comme un chef."
        );
        $createCourse(
            $cuisine,
            "Leçon n°1 : Mettre en oeuvre le style dans l'assiette",
            'lecon-1-mettre-en-oeuvre-style-assiette',
            "26.00",
            "Découvrez les principes de base du dressage culinaire."
        );

        $createCourse(
            $cuisine,
            "Leçon n°2 : Harmoniser un repas à quatre plats",
            'lecon-2-harmoniser-repas-quatre-plats',
            "26.00",
            "Apprenez les techniques de dressage pour sublimer vos plats."
        );

        // -------------------------------- Musique ----------------------------------------------

        $musique = new Category();
        $musique->setName('Musique');
        $musique->setSlug('musique');
        $manager->persist($musique);

        $createCourse(
            $musique,
            "Cursus d'initiation à la guitare",
            'cursus-initiation-guitare',
            "50.00",
            "Apprenez les bases de la guitare pour jouer vos morceaux préférés."
        );

        $createCourse(
            $musique,
            "Leçon n°1 : Découverte de l'instrument",
            'lecon-1-decouverte-instrument',
            "28.00",
            "Apprenez les accords essentiels pour débuter à la guitare."
        );


        $createCourse(
            $musique,
            "Leçon n°2 : Les accords et les gammes",
            'lecon-2-accords-gammes',
            "26.00",
            "Apprenez les accords essentiels pour débuter à la guitare."
        );

        $createCourse(
            $musique,
            "Cursus d'initiation au piano",
            'cursus-initiation-piano',
            "50.00",
            "Apprenez les bases du piano pour jouer vos morceaux préférés."
        );
        $createCourse(
            $musique,
            "Leçon n°1 : Découverte de l'instrument",
            'lecon-1-decouverte-instrument-piano',
            "26.00",
            "Apprenez les bases du piano et découvrez l'instrument."
        );

        $createCourse(
            $musique,
            "Leçon n°2 : Les accords et les gammes",
            'lecon-2-accords-gammes-piano',
            "26.00",
            "Apprenez les accords et les gammes pour bien débuter le piano."
        );

        $manager->flush();

        

    
    }
}

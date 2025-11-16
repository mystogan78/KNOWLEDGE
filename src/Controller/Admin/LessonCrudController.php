<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\String\Slugger\SluggerInterface;

class LessonCrudController extends AbstractCrudController
{
    public function __construct(private SluggerInterface $slugger) {}

    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Leçon')
            ->setEntityLabelInPlural('Leçons')
            ->setDefaultSort(['course' => 'ASC', 'position' => 'ASC'])
            ->setSearchFields(['title', 'slug', 'content', 'course.title']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('course'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('course', 'Cursus')->autocomplete();

        yield IntegerField::new('position', 'Position')
            ->setHelp('Ordre dans le cursus (1, 2, 3, …)');

        yield TextField::new('title', 'Titre');

        yield TextField::new('slug', 'Slug')
            ->setHelp('Laisser vide pour générer automatiquement');

        // Prix stocké en DECIMAL(8,2) dans l’entité => pas stocké en centimes
        yield MoneyField::new('price', 'Prix (€)')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);

        yield UrlField::new('videoUrl', 'URL vidéo')
            ->hideOnIndex()
            ->setHelp('Lien (YouTube, Vimeo, etc.)');

        // On garde l’éditeur visuel, mais on nettoiera le HTML à l’enregistrement
        yield TextEditorField::new('introText', 'Introduction')
            ->hideOnIndex();

        yield TextEditorField::new('content', 'Contenu')
            ->hideOnIndex()
            ->setHelp('Texte riche autorisé dans l’admin, mais nettoyé (sans HTML) avant sauvegarde.');
    }

    /** Nettoyage commun : strip tous les tags & trim */
    private function sanitize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $clean = strip_tags($value);          // enlève toutes les balises HTML
        $clean = html_entity_decode($clean);  // décode &nbsp; &amp; …
        // Optionnel : normaliser les espaces
        $clean = preg_replace('/[ \t]+/', ' ', $clean);
        $clean = preg_replace('/\R{3,}/', "\n\n", $clean); // pas plus de 2 retours ligne
        return trim($clean);
    }

    /** Génère le slug si vide + nettoie les champs avant insert */
    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof Lesson) {
            if (!$entityInstance->getSlug() && $entityInstance->getTitle()) {
                $entityInstance->setSlug(strtolower($this->slugger->slug($entityInstance->getTitle())));
            }

            // 🧹 supprime tout HTML avant sauvegarde
            $entityInstance->setIntroText($this->sanitize($entityInstance->getIntroText()));
            $entityInstance->setContent($this->sanitize($entityInstance->getContent()));
        }

        parent::persistEntity($em, $entityInstance);
    }

    /** Même nettoyage pour les updates */
    public function updateEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if ($entityInstance instanceof Lesson) {
            if (!$entityInstance->getSlug() && $entityInstance->getTitle()) {
                $entityInstance->setSlug(strtolower($this->slugger->slug($entityInstance->getTitle())));
            }

            // 🧹 supprime tout HTML avant sauvegarde
            $entityInstance->setIntroText($this->sanitize($entityInstance->getIntroText()));
            $entityInstance->setContent($this->sanitize($entityInstance->getContent()));
        }

        parent::updateEntity($em, $entityInstance);
    }
}

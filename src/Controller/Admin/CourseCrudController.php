<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CourseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return Course::class; }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular('Cursus')
            ->setEntityLabelInPlural('Cursus')
            ->setDefaultSort(['title' => 'ASC'])
            ->setSearchFields(['title','slug','description']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('category');
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('category');
        yield TextField::new('title');
        yield TextField::new('slug');
        yield MoneyField::new('price', 'Prix (en EUR)')
        ->setCurrency('EUR')
        ->setStoredAsCents(false);
        yield TextareaField::new('description')->hideOnIndex();
        yield TextareaField::new('introText')->hideOnIndex();
    }
}


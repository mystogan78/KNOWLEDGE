<?php
namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string { return Category::class; }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie')
            ->setEntityLabelInPlural('Catégories')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield TextField::new('slug');
        yield ColorField::new('themeColor')->hideOnIndex();
        yield TextareaField::new('heroText')->hideOnIndex();
        yield UrlField::new('heroVideoUrl')->hideOnIndex();

        // Si tu utilises la hiérarchie
        yield AssociationField::new('parent')->setRequired(false)->onlyOnForms();
    }
}

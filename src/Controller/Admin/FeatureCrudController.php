<?php

namespace App\Controller\Admin;

use App\Entity\Feature;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FeatureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Feature::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre (32 caractères max)'),
            TextField::new('subtitle', 'Sous-titre (32 caractères max)'),
            TextareaField::new('description', 'Description'),
            ImageField::new('illustration', 'Image')
                ->setBasePath('illustrations-feature/')
                ->setUploadDir('public/illustrations-feature')
                ->setFormType(FileUploadType::class)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            TextField::new('description_illustration', 'Description courte de l\'image'),
            BooleanField::new('isActive', 'A afficher')
        ];
    }

}

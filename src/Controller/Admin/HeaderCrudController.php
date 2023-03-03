<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre de l\'en-tête'),
            TextareaField::new('content', 'Contenu'),
            TextField::new('btnTitle', 'Titre du bouton'),
            TextField::new('btnUrl', 'Lien du bouton'),
            ImageField::new('illustration')
                ->setBasePath('illustrations-header/')
                ->setUploadDir('public/illustrations-header')
                ->setFormType(FileUploadType::class)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            BooleanField::new('isActive', 'Est actif'),

        ];
    }

}

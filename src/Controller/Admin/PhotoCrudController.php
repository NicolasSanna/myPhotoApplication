<?php

namespace App\Controller\Admin;

use App\Entity\Photo;
use App\Services\RegisterImageService;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PhotoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Photo::class;
    }

    private RegisterImageService $registerImageService;

    public function __construct(RegisterImageService $registerImageService)
    {
        $this->registerImageService = $registerImageService;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextEditorField::new('description'),
            ImageField::new('imageUrl')
                ->setBasePath('image_directory')
                ->setUploadDir('public/image_directory')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            MoneyField::new('price')->setCurrency('EUR'),
            FormField::addPanel('Upload Image')
                ->setHelp('Please upload an image')
                ->setCssClass('important'),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $photo = new Photo();
        $photo->setCreatedAt(new \DateTimeImmutable());

        return $photo;
    }
    // public function configureFields(string $pageName): iterable
    // {
    //     return [
    //         TextField::new('title'),
    //         TextEditorField::new('description'),
    //         TextField::new('imageUrl'),
    //         MoneyField::new('price')->setCurrency('EUR'),
    //     ];
    // }
    
}

<?php

namespace App\Form;

use App\Entity\Photo;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('title')
            ->add('imageUrl', FileType::class, [
                'required' => false,
                'attr' => ['class' => 'Form-component-input'],
                'constraints' => [
                    new File([
                        'maxSize' => '2000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Le site accepte uniquement les fichiers PNG et JPG',
                    ])
                ],
                'label' => 'Image',
                'data_class' => null,
            ])
            // ->add('metaInfo')
            ->add('price', IntegerType::class)
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('modifiedAt', null, [
                'widget' => 'single_text'
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}

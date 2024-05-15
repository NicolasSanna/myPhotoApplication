<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class PhotoAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Photo::class,
            'placeholder' => 'Rechercher une photo...',
            'choice_label' => 'title',
            'searchable_fields' => ['title', 'description', 'tags.name'],
            'query_builder' => function (PhotoRepository $photoRepository) {
                return $photoRepository->createQueryBuilder('photo');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
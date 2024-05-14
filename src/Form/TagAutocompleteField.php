<?php

namespace App\Form;

use App\Entity\Choix;
use App\Entity\Tag;
use App\Repository\ChoixRepository;
use App\Repository\TagRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class TagAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Tag::class,
            'placeholder' => 'Rechercher un tag...',
            'choice_label' => 'name',
            'searchable_fields' => ['name'],
            'query_builder' => function (TagRepository $tagRepository) {
                return $tagRepository->createQueryBuilder('name');
            },
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
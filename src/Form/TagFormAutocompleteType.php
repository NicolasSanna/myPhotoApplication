<?php

namespace App\Form;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagFormAutocompleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tag', TagAutocompleteField::class, [
                'label' => 'Recherche Tag',
                'placeholder' => 'Rechercher un tag...'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,  // Spécifiez null car nous n'avons pas de classe de données spécifique ici
        ]);
    }
}
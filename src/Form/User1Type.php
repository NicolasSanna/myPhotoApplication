<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class User1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                    'Client' => 'ROLE_CUSTOMER',
                    'Api' => 'TOKEN_API'
                    // Ajouter d'autres rôles si nécessaire
                ],
                'multiple' => true,
                'expanded' => true,
                // 'expanded' => false, // Utiliser un dropdown plutôt que des checkboxes
            ])
            ->add('password')
            ->add('token')
            ->add('tokenExpireAt', null, [
                'widget' => 'single_text',
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

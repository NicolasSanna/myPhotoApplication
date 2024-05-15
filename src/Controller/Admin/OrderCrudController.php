<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\OrderItemType;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Entity\User;

class OrderCrudController extends AbstractCrudController
{
    public Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('deliveryAddress'),
            DateTimeField::new('createdAt'),
            DateTimeField::new('modifiedAt'),
            CollectionField::new('orderItems', 'Ordered items')
                ->setEntryType(OrderItemType::class)
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),
            
            
        ];
    }

    public function persistEntity($entityManager, $entityInstance): void
    {
        $this->addFlash('success', 'Order created/updated successfully!');

        // Access the user (assuming you have injected Security service)
        /**
         * @var User $user
         */
        $user = $this->security->getUser();

        if ($user && $user->getCustomer()) 
        {
            $entityInstance->setCustomer($user->getCustomer()); // Set the customer relationship
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
    
}

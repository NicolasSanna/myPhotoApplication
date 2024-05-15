<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException(); // Or redirect to a login page
        }
        return $this->render('admin/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('MyPhotoApplication');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        yield MenuItem::linkToCrud('Photos', 'fas fa-list', Photo::class);
        yield MenuItem::linkToCrud('Tags', 'fas fa-list', Tag::class);
        yield MenuItem::linkToCrud('Customers', 'fas fa-list', Customer::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-list', Order::class);
        yield MenuItem::linkToCrud('Commandes/Articles', 'fas fa-list', OrderItem::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', User::class);
    }
}

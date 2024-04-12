<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderItem;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CUSTOMER')]
#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/order/create', name: 'order_create')]
    public function createOrder(SessionInterface $session, PhotoRepository $photoRepository,  EntityManagerInterface $entityManager, UserRepository $userRepository, CustomerRepository $customerRepository): Response
    {
        // Récupérer les éléments du panier depuis la session
        $cart = $session->get('cart', []);

        $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);

        $customer = $customerRepository->findOneBy(['id' => $user->getCustomer()->getId()]);

        // Créer un nouvel objet Order
        $order = new Order();
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setCustomer($customer);
        // Définir l'adresse de livraison (ici email)
        $order->setDeliveryAddress($user->getEmail());

        // Récupérer les photos associées à chaque élément du panier
        foreach ($cart as $cartItem) 
        {
            // Récupérer la photo depuis le repository
            $photo = $photoRepository->find($cartItem['id']);

            if ($photo) 
            {
                // Créer un nouvel objet OrderItem
                $orderItem = new OrderItem();
                $orderItem->setPhoto($photo);
                $orderItem->setQuantity($cartItem['quantity']);
                $orderItem->setPrice($photo->getPrice()); // Utilisez le prix de la photo

                // Ajouter l'élément de commande à l'objet Order
                $order->addOrderItem($orderItem);
            }
        }

            $entityManager->persist($order);
            $entityManager->flush();

        // Effacer le panier de session après la création de la commande
        $session->set('cart', []);

        // Rediriger vers une page de confirmation ou de récapitulatif de commande
        return $this->redirectToRoute('order_confirmation');
    }

    #[Route('/order/confirmation', name: 'order_confirmation')]
    public function confirmation(): Response
    {
        return $this->render('order/confirmation.html.twig');
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }


}

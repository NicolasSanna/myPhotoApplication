<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderItem;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\PhotoRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_CUSTOMER")'))]
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_CUSTOMER")'))]
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

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_CUSTOMER")'))]
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

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_CUSTOMER")'))]
    #[Route('/order/confirmation', name: 'order_confirmation')]
    public function confirmation(OrderRepository $orderRepository): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        /**
         * @var User $user
         */
        $user = $this->getUser();

        // Récupérer le client associé à l'utilisateur
        $customer = $user->getCustomer();

        // Récupérer la dernière commande du client
        $lastOrder = $orderRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);
        // Vérifier si une commande a été trouvée
        if (!$lastOrder) 
        {
            // Gérer le cas où aucune commande n'a été trouvée
            // Par exemple, rediriger vers une page d'erreur ou afficher un message approprié
        }
        return $this->render('order/confirmation.html.twig', [
            'lastOrder' => $lastOrder
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_CUSTOMER")'))]
    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_CUSTOMER")'))]
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

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_CUSTOMER")'))]
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

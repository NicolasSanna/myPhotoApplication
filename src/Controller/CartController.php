<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private CartService $cartService;
    
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods:['GET'])]
    public function addToCart(int $id): JsonResponse
    {
        return $this->cartService->addToCart($id);
    }

    #[Route('/cart', name: 'cart_index')]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/cart/async', name: 'cart_index_async')]
    public function indexAsync(SessionInterface $session): JsonResponse
    {
        $cart = $session->get('cart', []);

        return new JsonResponse(['cart' => $cart]);
    }

    #[Route('/cart/remove/{id}/{quantity}', name: 'app_cart_remove')]
    public function removeFromCart(Request $request): RedirectResponse
    {
        $id = (int) $request->get('id');
        $quantity = (int) $request->get('quantity');

        $this->cartService->removeFromCart($id, $quantity);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/add/quantity/{id}', name: 'app_cart_add_quantity')]
    public function addToCartQuantity(Request $request): RedirectResponse
    {
        $id = (int) $request->get('id');

        $this->cartService->addToCartQuantity($id);

        return $this->redirectToRoute('cart_index');
    }
}
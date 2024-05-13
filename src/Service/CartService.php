<?php

namespace App\Service;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService implements CartServiceInterface
{
    private RequestStack $requestStack;
    private PhotoRepository $photoRepository;

    public function __construct(RequestStack $requestStack, PhotoRepository $photoRepository)
    {
        $this->requestStack = $requestStack;
        $this->photoRepository = $photoRepository;
    }

    public function addToCart(int $id): JsonResponse
    {
        $session = $this->requestStack->getSession(); // Get session from RequestStack

        $photo = $this->photoRepository->find($id);

        if (!$photo instanceof Photo) 
        {
            return new JsonResponse(['error' => 'Photo not found'], Response::HTTP_NOT_FOUND);
        }

        $cart = $session->get('cart', []);

        foreach ($cart as &$item) 
        {
            if ($item['id'] === $photo->getId()) 
            {
                $item['quantity']++;
                $session->set('cart', $cart);
                return new JsonResponse(['cart' => $cart]);
            }
        }

        $cart[] = [
            'id' => $photo->getId(),
            'title' => $photo->getTitle(),
            'price' => $photo->getPrice(),
            'quantity' => 1
        ];

        $session->set('cart', $cart);

        return new JsonResponse(['cart' => $cart]);
    }

    public function removeFromCart(int $id, int $quantity): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        // Find the index of the item to remove in the cart
        $index = array_search($id, array_column($cart, 'id'));

        // If the item exists in the cart
        if ($index !== false) 
        {
            // If the quantity to remove is less than or equal to the quantity in the cart
            if ($quantity <= $cart[$index]['quantity']) 
            {
                // Decrement the quantity in the cart
                $cart[$index]['quantity'] -= $quantity;

                // If the quantity is reduced to 0, remove the item completely from the cart
                if ($cart[$index]['quantity'] <= 0) {
                    unset($cart[$index]);
                }
            }

            // Reindex the array after modification
            $session->set('cart', array_values($cart));
        }
    }

    public function addToCartQuantity(int $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        // Find the index of the item to add quantity to in the cart
        $index = array_search($id, array_column($cart, 'id'));

        if ($index !== false) 
        {
            $cart[$index]['quantity']++;
        }

        $session->set('cart', $cart);
    }
}
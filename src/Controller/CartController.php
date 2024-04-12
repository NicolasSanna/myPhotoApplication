<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Photo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'app_cart_add', methods:['GET'])]
    public function addToCart(SessionInterface $session, PhotoRepository $photoRepository, int $id): Response
    {
        // Récupérer la photo depuis son ID
        $photo = $photoRepository->find($id);

        // Vérifier si la photo existe
        if (!$photo instanceof Photo) 
        {
            return new JsonResponse(['error' => 'Photo not found'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer le panier depuis la session
        $cart = $session->get('cart', []);

        // Vérifier si la photo est déjà dans le panier
        foreach ($cart as &$item) 
        {
            if ($item['id'] === $photo->getId()) 
            {
                $item['quantity']++;
                $session->set('cart', $cart);
                return new JsonResponse(['cart' => $cart]);
            }
        }

        // Si la photo n'est pas encore dans le panier, l'ajouter
        $cart[] = [
            'id' => $photo->getId(),
            'title' => $photo->getTitle(),
            'price' => $photo->getPrice(),
            'quantity' => 1
        ];

        // Mettre à jour le panier dans la session
        $session->set('cart', $cart);

        // Renvoyer une réponse JSON contenant les données du panier mises à jour
        return new JsonResponse(['cart' => $cart]);
    }

    #[Route('/cart', name: 'cart_index')]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

        #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart(SessionInterface $session, int $id): RedirectResponse
    {
        $cart = $session->get('cart', []);

        // Recherche de l'index de l'élément à supprimer dans le panier
        $index = array_search($id, array_column($cart, 'id'));

        // Suppression de l'élément du panier s'il existe
        if ($index !== false) 
        {
            unset($cart[$index]);
            $session->set('cart', array_values($cart)); // Réindexer le tableau après la suppression
        }

        return $this->redirectToRoute('cart_index');
    }
}
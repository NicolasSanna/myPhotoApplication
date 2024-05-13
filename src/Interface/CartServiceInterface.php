<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

interface CartServiceInterface
{
    public function addToCart(int $id): JsonResponse;
    public function removeFromCart(int $id, int $quantity): void;
    public function addToCartQuantity(int $id): void;
}
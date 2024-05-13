<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TagRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PhotoRepository $photoRepository): Response
    {
        $photos = $photoRepository->findAll();

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'cart' => '',
        ]);
    }

    public function menu(TagRepository $tagRepository): Response
    {
        $topTags = $tagRepository->getTopTagsWithPhotoCount();

        $test = 'test'; // Définition de la variable test

        // Passez les données du menu à toutes les vues en utilisant le service Twig
        return $this->render('_partials/menu.html.twig', [
            'topTags' => $topTags,
            'test' => $test, // Passer la variable test au modèle Twig
        ]);
    }
}

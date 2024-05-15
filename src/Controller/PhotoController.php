<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use App\Services\RegisterImageService;
use App\Form\PhotoFormAutocompleteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/photo')]
class PhotoController extends AbstractController
{
    #[Route('/', name: 'app_photo_index', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository): Response
    {
        $form = $this->createForm(PhotoFormAutocompleteType::class);
        return $this->render('photo/index.html.twig', [
            'photos' => $photoRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/searchautocomplete', name: 'app_photo_index_autocomplete', methods: ['GET', 'POST'])]
    public function searchAutocomplete(Request $request): Response
    {  
        $form = $this->createForm(PhotoFormAutocompleteType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
             // Récupère l'objet Tag sélectionné
            /**
             * @var Photo $photo
             */
            $photo = $form->get('photo')->getData();

            if ($photo) 
            {
                return $this->redirectToRoute('app_photo_show', ['id' => $photo->getId()]);
            }
        }
        return $this->render('photo/search.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_photo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RegisterImageService $registerImageService): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($form->get('title')->getData());

            $registerImageService->setForm($form);
            $fileName = $registerImageService->saveImage();

            $photo->setImageUrl($fileName);
            $photo->setSlug($slug);
            $entityManager->persist($photo);
            $entityManager->flush();

            return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photo/new.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }
    
    #[Route('/view/{id}', name: 'app_photo_show', methods: ['GET'])]
    public function show(Photo $photo): Response
    {
        return $this->render('photo/show.html.twig', [
            'photo' => $photo,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/edit/{slug}', name: 'app_photo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Photo $photo, EntityManagerInterface $entityManager, RegisterImageService $registerImageService): Response
    {
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $registerImageService->setForm($form);
            $fileName = $registerImageService->saveImage();
            $photo->setImageUrl($fileName);

            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($form->get('title')->getData());
            $photo->setSlug($slug);
            $entityManager->flush();

            return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('photo/edit.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{slug}', name: 'app_photo_delete', methods: ['POST'])]
    public function delete(Request $request, Photo $photo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$photo->getSlug(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($photo);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
    }
}

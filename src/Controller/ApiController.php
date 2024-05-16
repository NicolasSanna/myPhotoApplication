<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Photo;
use App\Repository\TagRepository;
use App\Repository\PhotoRepository;
use App\Services\RegisterImageApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_API')]
class ApiController extends AbstractController
{

    #[Route('/api/test-api', name: 'app_api', methods:['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(['message' => 'COUCOU']);
    }

    #[Route('/api/photos/add', name: 'app_api_photo_add', methods:['POST'])]
    public function add_photo_api(Request $request,EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer,TagRepository $tagRepository, RegisterImageApiService $registerImageApiService): JsonResponse 
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        $photo = new Photo();
        $photo->setDescription($data['description'] ?? null);
        $photo->setTitle($data['title'] ?? '');
        $photo->setPrice($data['price'] ?? 0);
        $photo->setMetaInfo($data['metaInfo'] ?? null);
        $photo->setCreatedAt(new \DateTimeImmutable());
        $photo->setModifiedAt(new \DateTimeImmutable());
        $photo->setSlug($data['slug'] ?? '');

        // Sauvegarde de l'image depuis l'URL ou le chemin local
        if (isset($data['imageUrl'])) {
            $imageFileName = $registerImageApiService->saveImage($data['imageUrl']);
            if ($imageFileName) {
                $photo->setImageUrl($imageFileName);
            } else {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Failed to save image from URL or local path',
                ], 400);
            }
        }

        // Gestion des tags
        if (isset($data['tags'])) {
            foreach ($data['tags'] as $tagName) {
                $tag = $tagRepository->findOneBy(['name' => $tagName]);
                if (!$tag) {
                    $tag = new Tag();
                    $tag->setName($tagName);
                    $entityManager->persist($tag);
                }
                $photo->addTag($tag);
            }
        }

        $errors = $validator->validate($photo);
        if (count($errors) > 0) {
            return $this->json([
                'status' => 'error',
                'errors' => (string) $errors,
            ], 400);
        }

        $entityManager->persist($photo);
        $entityManager->flush();

        return $this->json($photo, 201, [], ['groups' => 'photo_details']);
    }

    #[Route('/api/photos', name: 'app_api_photos', methods:['GET'])]
    public function get_photos_api(Request $request, PhotoRepository $photoRepository): JsonResponse
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);
        $query = $data['query'] ?? '';

        $photos = $photoRepository->searchPhotos($query);

        return $this->json($photos, 200, [], ['groups' => 'photo_details']);
    }

    
    #[Route('/api/tag/search', name: 'app_api_photos', methods:['GET'])]
    public function get_tags_api(Request $request, TagRepository $tagRepository): JsonResponse
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);
        $query = $data['query'] ?? '';

        $tag = $tagRepository->searchByName($query);

        return $this->json($tag, 200, [], ['groups' => 'tag_details']);
    }

    #[Route('/api/tag/add', name: 'app_api_tag_add', methods:['POST'])]
   public function add_tag_api(Request $request, TagRepository $tagRepository, EntityManagerInterface $entityManagerInterface, ValidatorInterface $validator): JsonResponse
   {
        $json = json_decode($request->getContent(), true);

        // Vérification si le JSON est valide
        if ($json === null) {
            return $this->json(['message' => 'Invalid JSON'], 400);
        }

        $tag = new Tag();
        $tag->setName($json['name']);

        $errors = $validator->validate($tag);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['message' => 'Validation error', 'errors' => $errorMessages], 400);

            
        }

        $entityManagerInterface->persist($tag);
        $entityManagerInterface->flush();

        return $this->json(['message' => 'Tag créé avec succès !']);
   }
}

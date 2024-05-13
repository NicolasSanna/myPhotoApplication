<?php 

namespace App\Service;

use App\Repository\TagRepository;

class TopTagsService
{
    private array $topTags;

    public function __construct(TagRepository $tagRepository)
    {
        $this->topTags = $tagRepository->getTopTagsWithPhotoCount();
    }

    public function getTopTags(): array
    {
        return $this->topTags;
    }
}
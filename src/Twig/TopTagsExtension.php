<?php

namespace App\Twig;

use App\Service\TopTagsService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TopTagsExtension extends AbstractExtension
{
    private $topTagsService;

    public function __construct(TopTagsService $topTagsService)
    {
        $this->topTagsService = $topTagsService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_top_tags', [$this->topTagsService, 'getTopTags']),
        ];
    }
}
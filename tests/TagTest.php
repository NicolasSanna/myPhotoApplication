<?php

namespace App\Tests\Entity;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        //$this->entityManager = null;
    }

    public function testTagCreation(): void
    {
        // Création d'une nouvelle instance de Tag
        $tag = new Tag();
        $tag->setName('TestTag');

        // Persist de l'entité
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        // Vérification que l'entité a été enregistrée en base de données
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $savedTag = $tagRepository->findOneBy(['name' => 'TestTag']);

        $this->assertNotNull($savedTag);
        $this->assertSame('TestTag', $savedTag->getName());
    }
}
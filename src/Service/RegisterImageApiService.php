<?php

namespace App\Services;

use Symfony\Component\Filesystem\Filesystem;

class RegisterImageApiService
{
    private string $imageDirectory;
    private Filesystem $filesystem;

    public function __construct(string $imageDirectory, Filesystem $filesystem)
    {
        $this->imageDirectory = $imageDirectory;
        $this->filesystem = $filesystem;
    }

    public function saveImage(string $imagePath): ?string
    {
        // Vérifier si le chemin est une URL ou un chemin de fichier local
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) 
        {
            return $this->saveImageFromUrl($imagePath);
        } 
        elseif (file_exists($imagePath)) 
        {
            
            return $this->saveImageFromLocalPath($imagePath);
        } 
        else 
        {
            return null;
        }
    }

    private function saveImageFromUrl(string $imageUrl): ?string
    {
        $imageContents = file_get_contents($imageUrl);
        
        if ($imageContents === false)
        {
            return null;
        }

        $originalFileName = basename(parse_url($imageUrl, PHP_URL_PATH));
        return $this->saveImageContents($originalFileName, $imageContents);
    }

    private function saveImageFromLocalPath(string $localPath): ?string
    {
        $imageContents = file_get_contents($localPath);
        if ($imageContents === false) 
        {
            return null;
        }

        $originalFileName = basename($localPath);

        return $this->saveImageContents($originalFileName, $imageContents);
    }

    private function saveImageContents(string $originalFileName, string $imageContents): ?string
    {
        // Génération d'un nom de fichier aléatoire unique
        $randomFileName = uniqid() . '-' . $originalFileName;

        // Chemin complet du fichier
        $filePath = $this->imageDirectory . '/' . $randomFileName;

        // Écriture du contenu de l'image dans le fichier
        try 
        {
            $this->filesystem->dumpFile($filePath, $imageContents);
        } 
        catch (\Exception $e) 
        {
            // Gérer les erreurs d'écriture du fichier
            dd($e->getMessage());
        }

        // Retourner le nom du fichier enregistré
        return $randomFileName;
    }
}
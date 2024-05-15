<?php

namespace App\Services;

use Symfony\Component\Form\FormInterface;

class RegisterImageService
{
    private ?FormInterface $form = null;
    private string $imageDirectory;

    public function __construct(string $imageDirectory)
    {
        $this->imageDirectory = $imageDirectory;
    }

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    public function saveImage(): ?string
    {
        if ($this->form === null) {
            return null;
        }

        // Récupération du fichier image
        $file = $this->form->get('imageUpload')->getData();
        
        if (!$file) {
            return null; // Si aucun fichier n'est uploadé, retourner null
        }

        // Récupération du nom original du fichier
        $originalFileName = $file->getClientOriginalName();

        // Nettoyage du nom du fichier (s'il y a des espaces, des points, des caractères spéciaux etc.)
        $cleanedFileName = preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($originalFileName));

        // Génération d'une chaîne de caractères aléatoire
        $randomString = bin2hex(random_bytes(5));

        // Récupération du nom du fichier sans l'extension
        $filenameWithoutExtension = pathinfo($cleanedFileName, PATHINFO_FILENAME);

        // Récupération de l'extension
        $extension = $file->getClientOriginalExtension();

        // Concaténation du nom du fichier avec la chaîne de caractères aléatoire et l'extension.
        $randomFileName = $filenameWithoutExtension . '-' . $randomString . '.' . $extension;

        // Déplacement du fichier vers le répertoire spécifié
        $file->move($this->imageDirectory, $randomFileName);

        // Renvoi du nom du fichier final afin de le préparer pour son insertion dans la base de données.
        return $randomFileName;
    }
}
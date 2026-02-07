<?php

namespace App\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateNewsRequest
{
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Le titre doit faire au moins 5 caractères.")]
    public ?string $title = null;

    #[Assert\NotBlank(message: "La catégorie est obligatoire.")]
    #[Assert\Choice(choices: ['événements', 'vie', 'réussites', 'partenariats'], message: "Catégorie invalide.")]
    public ?string $category = null;

    #[Assert\NotBlank(message: "Le contenu est obligatoire.")]
    #[Assert\Length(min: 50, minMessage: "Le contenu doit faire au moins 50 caractères.")]
    public ?string $content = null;

    #[Assert\Url(message: "L'URL de l'image n'est pas valide.")]
    public ?string $imageUrl = null;

    #[Assert\Type(type: 'bool')]
    public bool $publish = false;
}

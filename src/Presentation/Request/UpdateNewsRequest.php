<?php

namespace App\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateNewsRequest
{
    #[Assert\Length(min: 5, max: 255)]
    public ?string $title = null;

    #[Assert\Choice(choices: ['événements', 'vie', 'réussites', 'partenariats'])]
    public ?string $category = null;

    #[Assert\Length(min: 50)]
    public ?string $content = null;

    public ?string $imageUrl = null;

    #[Assert\Choice(choices: ['draft', 'published'])]
    public ?string $status = null;
}

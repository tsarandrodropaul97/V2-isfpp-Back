<?php

namespace App\Application\DTO;

/**
 * =================================================================
 * APPLICATION - DTO
 * =================================================================
 * 
 * 🎯 OBJECTIF : Porter les données pour la création d'une actualité.
 */
class CreateNewsDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $category,
        public readonly string $content,
        public readonly ?string $imageUrl,
        public readonly bool $publish
    ) {}
}

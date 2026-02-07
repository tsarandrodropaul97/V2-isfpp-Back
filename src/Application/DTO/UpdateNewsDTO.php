<?php

namespace App\Application\DTO;

/**
 * =================================================================
 * APPLICATION - DTO
 * =================================================================
 * 
 * 🎯 OBJECTIF : Porter les données pour la mise à jour d'une actualité.
 */
class UpdateNewsDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $title,
        public readonly ?string $category,
        public readonly ?string $content,
        public readonly ?string $imageUrl,
        public readonly ?string $status
    ) {}
}

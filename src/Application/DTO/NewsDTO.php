<?php

namespace App\Application\DTO;

use App\Domain\Entity\News;

/**
 * =================================================================
 * APPLICATION - DTO
 * =================================================================
 * 
 * 🎯 OBJECTIF : Transférer les données de l'actualité vers l'extérieur (API).
 * 
 * 💡 POURQUOI : Découplage. On n'expose pas directement l'entité du domaine.
 */
class NewsDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly string $category,
        public readonly string $categoryLabel,
        public readonly string $categoryColor,
        public readonly string $content,
        public readonly ?string $imageUrl,
        public readonly string $status,
        public readonly string $statusLabel,
        public readonly ?string $publishedAt,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}

    public static function fromDomain(News $news): self
    {
        return new self(
            $news->getId(),
            $news->getTitle(),
            $news->getSlug(),
            $news->getCategory()->value,
            $news->getCategory()->getLabel(),
            $news->getCategory()->getColor(),
            $news->getContent(),
            $news->getImageUrl(),
            $news->getStatus()->value,
            $news->getStatus()->getLabel(),
            $news->getPublishedAt()?->format('Y-m-d H:i:s'),
            $news->getCreatedAt()->format('Y-m-d H:i:s'),
            $news->getUpdatedAt()->format('Y-m-d H:i:s')
        );
    }
}

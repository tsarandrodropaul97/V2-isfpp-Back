<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\NewsCategory;
use App\Domain\ValueObject\NewsStatus;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * =================================================================
 * DOMAIN - ENTITY
 * =================================================================
 * 
 * 🎯 OBJECTIF : Représenter le concept métier d'une Actualité.
 * 
 * 💡 POURQUOI : C'est le cœur de la logique métier, indépendant de la base de données ou du framework.
 * 
 * 📚 PRINCIPE SOLID : SRP - Contient uniquement la logique métier et les invariants de l'actualité.
 */
class News
{
    private ?int $id = null;
    private string $title;
    private string $slug;
    private NewsCategory $category;
    private string $content;
    private ?string $imageUrl;
    private NewsStatus $status;
    private ?DateTimeImmutable $publishedAt = null;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        string $title,
        NewsCategory $category,
        string $content,
        ?string $imageUrl
    ) {
        $this->ensureTitleIsValid($title);
        $this->ensureContentIsValid($content);

        $this->title = $title;
        $this->slug = $this->generateSlug($title);
        $this->category = $category;
        $this->content = $content;
        $this->imageUrl = $imageUrl;
        $this->status = NewsStatus::DRAFT;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Factory method pour créer une nouvelle actualité.
     */
    public static function create(
        string $title,
        NewsCategory $category,
        string $content,
        ?string $imageUrl = null
    ): self {
        return new self($title, $category, $content, $imageUrl);
    }

    public function publish(): void
    {
        if ($this->status === NewsStatus::PUBLISHED) {
            throw new InvalidArgumentException("L'actualité est déjà publiée.");
        }
        $this->status = NewsStatus::PUBLISHED;
        $this->publishedAt = new DateTimeImmutable();
        $this->updateTimestamp();
    }

    public function unpublish(): void
    {
        $this->status = NewsStatus::DRAFT;
        $this->updateTimestamp();
    }

    public function updateTitle(string $title): void
    {
        $this->ensureTitleIsValid($title);
        $this->title = $title;
        $this->slug = $this->generateSlug($title);
        $this->updateTimestamp();
    }

    public function updateContent(string $content): void
    {
        $this->ensureContentIsValid($content);
        $this->content = $content;
        $this->updateTimestamp();
    }

    public function updateCategory(NewsCategory $category): void
    {
        $this->category = $category;
        $this->updateTimestamp();
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
        $this->updateTimestamp();
    }

    private function ensureTitleIsValid(string $title): void
    {
        if (mb_strlen($title) < 5 || mb_strlen($title) > 255) {
            throw new InvalidArgumentException("Le titre doit contenir entre 5 et 255 caractères.");
        }
    }

    private function ensureContentIsValid(string $content): void
    {
        if (mb_strlen($content) < 50) {
            throw new InvalidArgumentException("Le contenu doit contenir au moins 50 caractères.");
        }
    }

    private function generateSlug(string $text): string
    {
        // Simple slug generation logic
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getSlug(): string { return $this->slug; }
    public function getCategory(): NewsCategory { return $this->category; }
    public function getContent(): string { return $this->content; }
    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function getStatus(): NewsStatus { return $this->status; }
    public function getPublishedAt(): ?DateTimeImmutable { return $this->publishedAt; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function isPublished(): bool { return $this->status === NewsStatus::PUBLISHED; }
    public function isDraft(): bool { return $this->status === NewsStatus::DRAFT; }
}

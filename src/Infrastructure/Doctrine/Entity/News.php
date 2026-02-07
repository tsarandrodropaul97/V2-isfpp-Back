<?php

namespace App\Infrastructure\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * =================================================================
 * INFRASTRUCTURE - DOCTRINE ENTITY
 * =================================================================
 * 
 * 🎯 OBJECTIF : Représenter la table 'news' dans la base de données.
 * 
 * 💡 POURQUOI : Séparation Clean Architecture. Cette classe est liée à l'ORM (Doctrine), contrairement à l'entité Domain.
 */
#[ORM\Entity]
#[ORM\Table(name: 'news')]
#[ORM\Index(name: 'idx_news_status', columns: ['status'])]
#[ORM\Index(name: 'idx_news_category', columns: ['category'])]
#[ORM\Index(name: 'idx_news_published_at', columns: ['published_at'])]
#[ORM\HasLifecycleCallbacks]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 100)]
    private string $category;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $publishedAt = null;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }

    // Getters and Setters for Hydrator/Mapper

    public function getId(): ?int { return $this->id; }
    
    // Pour le mapper : on doit pouvoir setter l'ID si nécessaire (rarement) ou juste le lire.
    // Doctrine hydrate l'ID via réflexion.

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }

    public function getCategory(): string { return $this->category; }
    public function setCategory(string $category): self { $this->category = $category; return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function setImageUrl(?string $imageUrl): self { $this->imageUrl = $imageUrl; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getPublishedAt(): ?DateTime { return $this->publishedAt; }
    public function setPublishedAt(?DateTime $publishedAt): self { $this->publishedAt = $publishedAt; return $this; }

    public function getCreatedAt(): DateTime { return $this->createdAt; }
    public function setCreatedAt(DateTime $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): DateTime { return $this->updatedAt; }
    public function setUpdatedAt(DateTime $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
}

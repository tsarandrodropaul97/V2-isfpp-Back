<?php

namespace App\Infrastructure\Persistence\MySQL;

use App\Domain\Entity\News as DomainNews;
use App\Infrastructure\Doctrine\Entity\News as DoctrineNews;
use App\Domain\ValueObject\NewsCategory;
use App\Domain\ValueObject\NewsStatus;
use DateTimeImmutable;
use ReflectionClass;

/**
 * =================================================================
 * INFRASTRUCTURE - MAPPER
 * =================================================================
 * 
 * 🎯 OBJECTIF : Convertir l'entité Domain <-> Entité Doctrine.
 * 
 * 💡 POURQUOI : Clean Architecture. L'entité Domain ne doit pas connaître l'ORM.
 *              L'ORM a besoin de sa propre entité pour le mapping DB.
 */
class NewsMapper
{
    public function toDomain(DoctrineNews $doctrineNews): DomainNews
    {
        // Utilisation de la réflexion pour instancier l'entité Domain (constructeur privé)
        // et pour setter les propriétés sans setters publics sur l'entité Domain.
        
        $reflection = new ReflectionClass(DomainNews::class);
        $domainNews = $reflection->newInstanceWithoutConstructor();

        $this->setPrivateProperty($domainNews, 'id', $doctrineNews->getId());
        $this->setPrivateProperty($domainNews, 'title', $doctrineNews->getTitle());
        $this->setPrivateProperty($domainNews, 'slug', $doctrineNews->getSlug());
        $this->setPrivateProperty($domainNews, 'content', $doctrineNews->getContent());
        $this->setPrivateProperty($domainNews, 'imageUrl', $doctrineNews->getImageUrl());
        
        // Enums
        $this->setPrivateProperty($domainNews, 'category', NewsCategory::from($doctrineNews->getCategory()));
        $this->setPrivateProperty($domainNews, 'status', NewsStatus::from($doctrineNews->getStatus()));

        // Dates
        $this->setPrivateProperty($domainNews, 'publishedAt', $doctrineNews->getPublishedAt() ? DateTimeImmutable::createFromMutable($doctrineNews->getPublishedAt()) : null);
        $this->setPrivateProperty($domainNews, 'createdAt', DateTimeImmutable::createFromMutable($doctrineNews->getCreatedAt()));
        $this->setPrivateProperty($domainNews, 'updatedAt', DateTimeImmutable::createFromMutable($doctrineNews->getUpdatedAt()));

        return $domainNews;
    }

    public function toInfrastructure(DomainNews $domainNews, ?DoctrineNews $doctrineNews = null): DoctrineNews
    {
        if (!$doctrineNews) {
            $doctrineNews = new DoctrineNews();
        }

        $doctrineNews->setTitle($domainNews->getTitle());
        $doctrineNews->setSlug($domainNews->getSlug());
        $doctrineNews->setCategory($domainNews->getCategory()->value);
        $doctrineNews->setContent($domainNews->getContent());
        $doctrineNews->setImageUrl($domainNews->getImageUrl());
        $doctrineNews->setStatus($domainNews->getStatus()->value);
        
        if ($domainNews->getPublishedAt()) {
            $doctrineNews->setPublishedAt(\DateTime::createFromImmutable($domainNews->getPublishedAt()));
        } else {
            $doctrineNews->setPublishedAt(null);
        }

        // createdAt/updatedAt gérés par Lifecycle callbacks sur Doctrine, 
        // ou on peut forcer si nécessaire. Ici on laisse Doctrine gérer updated.
        
        return $doctrineNews;
    }

    private function setPrivateProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}

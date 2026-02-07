<?php

namespace App\Domain\Repository;

use App\Domain\Entity\News;
use App\Domain\ValueObject\NewsCategory;
use App\Domain\ValueObject\NewsStatus;

/**
 * =================================================================
 * DOMAIN - REPOSITORY INTERFACE
 * =================================================================
 * 
 * 🎯 OBJECTIF : Définir le contrat pour l'accès aux données des actualités.
 * 
 * 💡 POURQUOI : Inversion de dépendance (DIP). Le domaine ne dépend pas de Doctrine, c'est l'infrastructure qui implémente cette interface.
 */
interface NewsRepositoryInterface
{
    public function save(News $news): News;
    public function findById(int $id): ?News;
    public function findBySlug(string $slug): ?News;
    public function findAll(): array;
    public function findPublished(?int $limit): array;
    public function findByCategory(NewsCategory $category, ?NewsStatus $status): array;
    public function delete(News $news): void;
    public function countByStatus(?NewsStatus $status): int;
}

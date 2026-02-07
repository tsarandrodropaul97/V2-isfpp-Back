<?php

namespace App\Application\UseCase;

use App\Application\DTO\NewsDTO;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\ValueObject\NewsCategory;
use App\Domain\ValueObject\NewsStatus;

/**
 * =================================================================
 * APPLICATION - USE CASE
 * =================================================================
 * 
 * 🎯 OBJECTIF : Récupérer les actualités par catégorie.
 */
class GetNewsByCategoryUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository
    ) {}

    public function execute(string $categoryValue, bool $publishedOnly = true): array
    {
        $category = NewsCategory::from($categoryValue);
        $status = $publishedOnly ? NewsStatus::PUBLISHED : null;

        $newsList = $this->newsRepository->findByCategory($category, $status);

        return array_map(
            fn($news) => NewsDTO::fromDomain($news),
            $newsList
        );
    }
}

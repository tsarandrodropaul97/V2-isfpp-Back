<?php

namespace App\Application\UseCase;

use App\Application\DTO\NewsDTO;
use App\Domain\Repository\NewsRepositoryInterface;

/**
 * =================================================================
 * APPLICATION - USE CASE
 * =================================================================
 * 
 * 🎯 OBJECTIF : Récupérer les actualités publiées (pour le FrontOffice).
 */
class GetPublishedNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository
    ) {}

    public function execute(?int $limit = null): array
    {
        $newsList = $this->newsRepository->findPublished($limit);

        return array_map(
            fn($news) => NewsDTO::fromDomain($news),
            $newsList
        );
    }
}

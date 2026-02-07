<?php

namespace App\Application\UseCase;

use App\Application\DTO\NewsDTO;
use App\Domain\Repository\NewsRepositoryInterface;

/**
 * =================================================================
 * APPLICATION - USE CASE
 * =================================================================
 * 
 * 🎯 OBJECTIF : Récupérer toutes les actualités (pour le BackOffice).
 */
class GetAllNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository
    ) {}

    public function execute(): array
    {
        $newsList = $this->newsRepository->findAll();

        return array_map(
            fn($news) => NewsDTO::fromDomain($news),
            $newsList
        );
    }
}

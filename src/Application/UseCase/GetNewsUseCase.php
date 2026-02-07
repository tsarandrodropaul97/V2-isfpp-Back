<?php

namespace App\Application\UseCase;

use App\Application\DTO\NewsDTO;
use App\Domain\Repository\NewsRepositoryInterface;
use InvalidArgumentException;

/**
 * =================================================================
 * APPLICATION - USE CASE
 * =================================================================
 * 
 * 🎯 OBJECTIF : Récupérer une actualité par son ID.
 */
class GetNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository
    ) {}

    public function execute(int $id): NewsDTO
    {
        $news = $this->newsRepository->findById($id);

        if (!$news) {
            throw new InvalidArgumentException("Actualité non trouvée.");
        }

        return NewsDTO::fromDomain($news);
    }
}

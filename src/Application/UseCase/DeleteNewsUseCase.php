<?php

namespace App\Application\UseCase;

use App\Domain\Repository\NewsRepositoryInterface;
use InvalidArgumentException;

/**
 * =================================================================
 * APPLICATION - USE CASE
 * =================================================================
 * 
 * 🎯 OBJECTIF : Supprimer une actualité.
 */
class DeleteNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository
    ) {}

    public function execute(int $id): void
    {
        $news = $this->newsRepository->findById($id);

        if (!$news) {
            throw new InvalidArgumentException("Actualité non trouvée.");
        }

        $this->newsRepository->delete($news);
    }
}

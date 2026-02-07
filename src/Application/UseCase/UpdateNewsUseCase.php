<?php

namespace App\Application\UseCase;

use App\Application\DTO\NewsDTO;
use App\Application\DTO\UpdateNewsDTO;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\ValueObject\NewsCategory;
use App\Domain\ValueObject\NewsStatus;
use InvalidArgumentException;

/**
 * =================================================================
 * APPLICATION - USE CASE
 * =================================================================
 * 
 * 🎯 OBJECTIF : Mettre à jour une actualité existante.
 */
class UpdateNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository
    ) {}

    public function execute(UpdateNewsDTO $dto): NewsDTO
    {
        $news = $this->newsRepository->findById($dto->id);

        if (!$news) {
            throw new InvalidArgumentException("Actualité non trouvée.");
        }

        if ($dto->title) {
            $news->updateTitle($dto->title);
        }

        if ($dto->content) {
            $news->updateContent($dto->content);
        }

        if ($dto->category) {
            $news->updateCategory(NewsCategory::from($dto->category));
        }

        if ($dto->imageUrl !== null) { // Permet de supprimer l'image si vide mais présent
            $news->setImageUrl($dto->imageUrl);
        }

        if ($dto->status) {
            $status = NewsStatus::from($dto->status);
            if ($status === NewsStatus::PUBLISHED && !$news->isPublished()) {
                $news->publish();
            } elseif ($status === NewsStatus::DRAFT && $news->isPublished()) {
                $news->unpublish();
            }
        }

        $this->newsRepository->save($news);

        return NewsDTO::fromDomain($news);
    }
}

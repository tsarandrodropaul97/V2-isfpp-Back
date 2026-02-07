<?php

namespace App\Application\UseCase;

use App\Application\DTO\CreateNewsDTO;
use App\Application\DTO\NewsDTO;
use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\ValueObject\NewsCategory;

/**
 * =================================================================
 * APPLICATION - USE CASE
 * =================================================================
 * 
 * 🎯 OBJECTIF : Orchestrer la création d'une actualité.
 * 
 * 💡 POURQUOI : Sépare la logique métier (création) de l'interface utilisateur (Controller).
 */
class CreateNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository
    ) {}

    public function execute(CreateNewsDTO $dto): NewsDTO
    {
        // Conversion string -> Enum
        $category = NewsCategory::from($dto->category);

        $news = News::create(
            $dto->title,
            $category,
            $dto->content,
            $dto->imageUrl
        );

        if ($dto->publish) {
            $news->publish();
        }

        $savedNews = $this->newsRepository->save($news);

        return NewsDTO::fromDomain($savedNews);
    }
}

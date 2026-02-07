<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\ValueObject\NewsCategory;
use App\Domain\ValueObject\NewsStatus;
use App\Infrastructure\Doctrine\Entity\News as DoctrineNews;
use App\Infrastructure\Persistence\MySQL\NewsMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * =================================================================
 * INFRASTRUCTURE - REPOSITORY IMPLEMENTATION
 * =================================================================
 * 
 * 🎯 OBJECTIF : Implémenter l'accès aux données via Doctrine ORM.
 */
class DoctrineNewsRepository extends ServiceEntityRepository implements NewsRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private NewsMapper $mapper
    ) {
        parent::__construct($registry, DoctrineNews::class);
    }

    public function save(News $news): News
    {
        $id = $news->getId();
        $doctrineNews = null;

        if ($id) {
            $doctrineNews = $this->find($id);
        }

        $doctrineNews = $this->mapper->toInfrastructure($news, $doctrineNews);

        $this->getEntityManager()->persist($doctrineNews);
        $this->getEntityManager()->flush();

        // Retourne l'entité du domaine rafraîchie (notamment pour l'ID si création)
        return $this->mapper->toDomain($doctrineNews);
    }

    public function findById(int $id): ?News
    {
        $doctrineNews = $this->find($id);
        return $doctrineNews ? $this->mapper->toDomain($doctrineNews) : null;
    }

    public function findBySlug(string $slug): ?News
    {
        $doctrineNews = $this->findOneBy(['slug' => $slug]);
        return $doctrineNews ? $this->mapper->toDomain($doctrineNews) : null;
    }

    public function findAll(): array
    {
        $doctrineNewsList = $this->findBy([], ['createdAt' => 'DESC']);
        return array_map([$this->mapper, 'toDomain'], $doctrineNewsList);
    }

    public function findPublished(?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('n')
            ->where('n.status = :status')
            ->setParameter('status', NewsStatus::PUBLISHED->value)
            ->orderBy('n.publishedAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        $doctrineNewsList = $qb->getQuery()->getResult();
        return array_map([$this->mapper, 'toDomain'], $doctrineNewsList);
    }

    public function findByCategory(NewsCategory $category, ?NewsStatus $status = null): array
    {
        $qb = $this->createQueryBuilder('n')
            ->where('n.category = :category')
            ->setParameter('category', $category->value)
            ->orderBy('n.createdAt', 'DESC');

        if ($status) {
            $qb->andWhere('n.status = :status')
               ->setParameter('status', $status->value);
        }

        $doctrineNewsList = $qb->getQuery()->getResult();
        return array_map([$this->mapper, 'toDomain'], $doctrineNewsList);
    }

    public function delete(News $news): void
    {
        $doctrineNews = $this->find($news->getId());
        if ($doctrineNews) {
            $this->getEntityManager()->remove($doctrineNews);
            $this->getEntityManager()->flush();
        }
    }

    public function countByStatus(?NewsStatus $status = null): int
    {
        $qb = $this->createQueryBuilder('n')
            ->select('count(n.id)');

        if ($status) {
            $qb->where('n.status = :status')
               ->setParameter('status', $status->value);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}

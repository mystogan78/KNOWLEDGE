<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }
    
    public function findAllOrderedByTitle(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function searchByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.title LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllCursus(): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.title) LIKE :word')
            ->setParameter('word', '%cursus%')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }





 
}

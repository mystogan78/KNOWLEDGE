<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    /** Toutes les leçons d’un cours, triées par position ASC */
    public function findByCourseOrdered(Course $course): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.course = :c')->setParameter('c', $course)
            ->orderBy('l.position', 'ASC')
            ->getQuery()->getResult();
    }

    /** Une leçon par slug */
    public function findOneBySlug(string $slug): ?Lesson
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.slug = :slug')->setParameter('slug', $slug)
            ->getQuery()->getOneOrNullResult();
    }

    /** Recherche insensible à la casse dans le titre (optionnellement par cours) */
    public function search(string $keyword, ?Course $course = null): array
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('LOWER(l.title) LIKE :kw')
            ->setParameter('kw', '%'.mb_strtolower($keyword).'%')
            ->orderBy('l.position', 'ASC');

        if ($course) {
            $qb->andWhere('l.course = :c')->setParameter('c', $course);
        }
        return $qb->getQuery()->getResult();
    }

    /** Leçon précédente dans le même cours (par position) */
    public function findPrevious(Lesson $lesson): ?Lesson
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.course = :c')->setParameter('c', $lesson->getCourse())
            ->andWhere('l.position < :p')->setParameter('p', $lesson->getPosition())
            ->orderBy('l.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /** Leçon suivante dans le même cours (par position) */
    public function findNext(Lesson $lesson): ?Lesson
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.course = :c')->setParameter('c', $lesson->getCourse())
            ->andWhere('l.position > :p')->setParameter('p', $lesson->getPosition())
            ->orderBy('l.position', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /** Compte des leçons d’un cours */
    public function countByCourse(Course $course): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->andWhere('l.course = :c')->setParameter('c', $course)
            ->getQuery()->getSingleScalarResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /** Tous les cours triés par titre */
    public function findAllOrderedByTitle(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Recherche (insensible à la casse) sur le titre */
    public function searchByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.title) LIKE :kw')
            ->setParameter('kw', '%'.mb_strtolower($keyword).'%')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Tous les “cursus” par convention (titre contenant “cursus”) */
    public function findAllCursus(): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.title) LIKE :word')
            ->setParameter('word', '%cursus%')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Par slug (pour les pages détail) */
    public function findOneBySlug(string $slug): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** Par catégorie simple (sans sous-catégories) */
    public function findByCategory(Category $category): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.category = :cat')
            ->setParameter('cat', $category)
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Par arbre de catégories (catégorie + sous-catégories).
     * Nécessite que Category ait parent/children et un CategoryRepository avec getAllDescendantIds().
     */
    public function findByCategoryTree(Category $root, CategoryRepository $categoryRepo): array
    {
        $ids = $categoryRepo->getAllDescendantIds($root);

        return $this->createQueryBuilder('c')
            ->andWhere('c.category IN (:ids)')
            ->setParameter('ids', $ids)
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Derniers cours créés (utile en home) */
    public function findLatest(int $limit = 6): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Liste avec tri dynamique + pagination.
     * $sort: 'title'|'price'|'created' ; $direction: 'ASC'|'DESC'
     */
    public function paginateCatalog(
        ?Category $category = null,
        int $page = 1,
        int $limit = 12,
        string $sort = 'title',
        string $direction = 'ASC'
    ): Paginator {
        $qb = $this->createQueryBuilder('c');

        if ($category) {
            $qb->andWhere('c.category = :cat')->setParameter('cat', $category);
        }

        $allowedSort = [
            'title'   => 'c.title',
            'price'   => 'c.price',
            'created' => 'c.id', // à remplacer par c.createdAt si tu as le champ
        ];
        $col = $allowedSort[$sort] ?? 'c.title';
        $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        $qb->orderBy($col, $dir)
           ->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return new Paginator($qb->getQuery(), true);
    }

    /** Charge un cours + ses leçons (fetch join) pour éviter le N+1 */
    public function findOneWithLessons(int $id): ?Course
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.lessons', 'l')->addSelect('l')
            ->andWhere('c.id = :id')->setParameter('id', $id)
            ->orderBy('l.position', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Liste (ou page) avec nombre de leçons par cours.
     * Renvoie un tableau de [Course, lessonCount].
     */
    public function findWithLessonCount(int $page = 1, int $limit = 12): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.lessons', 'l')
            ->addSelect('COUNT(l.id) AS HIDDEN lessonCount')
            ->groupBy('c.id')
            ->orderBy('c.title', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        // Retourne des objets Course ; pour compter, relis les hints depuis getScalarResult si besoin
        return $qb->getQuery()->getResult();
    }

    /**
     * Somme des prix des leçons d’un cours (utile pour un “upsell”).
     * Retourne un string decimal (ex "52.00") ou "0.00" si aucune leçon.
     */
    public function getLessonPriceSum(Course $course): string
    {
        $sum = $this->getEntityManager()->createQuery(
            'SELECT COALESCE(SUM(l.price), 0) FROM App\Entity\Lesson l WHERE l.course = :c'
        )->setParameter('c', $course)->getSingleScalarResult();

        // DQL retourne string decimal, on renvoie tel quel
        return $sum;
    }
}

<?php
// src/Repository/EnrollmentRepository.php
namespace App\Repository;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Enrollment>
 */
class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    /**
     * Retourne true si l'utilisateur possède un droit d'accès (enrollment) sur ce cours.
     */
    public function hasAccess(User $user, Course $course): bool
    {
        return (bool) $this->createQueryBuilder('e')
            ->select('1')
            ->andWhere('e.user = :u')
            ->andWhere('e.course = :c')
            ->setParameter('u', $user)
            ->setParameter('c', $course)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Variante utile si tu n’as que les IDs (évite 2 chargements d’entités).
     */
    public function hasAccessByIds(int $userId, int $courseId): bool
    {
        return (bool) $this->createQueryBuilder('e')
            ->select('1')
            ->andWhere('IDENTITY(e.user) = :uid')
            ->andWhere('IDENTITY(e.course) = :cid')
            ->setParameter('uid', $userId)
            ->setParameter('cid', $courseId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Course[] liste des cours achetés par l'utilisateur, triés par titre.
     */
    public function findCoursesByUser(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->select('c')
            ->innerJoin('e.course', 'c')
            ->andWhere('e.user = :u')
            ->setParameter('u', $user)
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Enrollment[] tous les enrollments d’un user (dernier accès en premier).
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :u')
            ->setParameter('u', $user)
            ->orderBy('e.grantedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

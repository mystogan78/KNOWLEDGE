<?php

namespace App\Entity;


use App\Repository\EnrollmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EnrollmentRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_user_course' , columns: ['user_id', 'course_id'])]
#[UniqueEntity(fields: ['user', 'course'], message: 'Cet accès existe déjà pour cet utilisateur.')]


class Enrollment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\Column (type: 'datetime_immutable')]
    private \DateTimeImmutable $grantedAt;

    public function getId(): ?int{ return $this->id; }

    public function getUser(): ?User{ return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this;}
    
    public function getCourse(): ?Course { return $this->course; }
    public function setCourse(?Course $course): self { $this->course = $course; return $this; }
    
    public function getGrantedAt(): \DateTimeImmutable { return $this->grantedAt; }
    public function setGrantedAt(\DateTimeImmutable $grantedAt): self { $this->grantedAt = $grantedAt; return $this; }



  
}

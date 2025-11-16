<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé.')]
#[ORM\Index(columns: ['course_id', 'position'], name: 'idx_lesson_course_position')]
class Lesson
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    // 👉 Rendez-le nullable si vous voulez pouvoir enregistrer une leçon sans contenu immédiat
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoUrl = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Course $course = null;

    #[Assert\Positive]
    #[ORM\Column(options: ['default' => 1])]
    private ?int $position = 1;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $introText = null;

    // Prix de la leçon → nécessaire pour achat unitaire
    #[Assert\NotBlank]
    // Exemple de validation simple : 0, 0.00, 12.50, etc.
    #[Assert\Regex(pattern: '/^\d{1,6}(\.\d{1,2})?$/', message: 'Format monétaire invalide.')]
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private string $price = '0.00'; // ex: "26.00"

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(?string $content): self { $this->content = $content; return $this; }

    public function getVideoUrl(): ?string { return $this->videoUrl; }
    public function setVideoUrl(?string $videoUrl): self { $this->videoUrl = $videoUrl; return $this; }

    public function getCourse(): ?Course { return $this->course; }
    public function setCourse(?Course $course): self { $this->course = $course; return $this; }

    public function getPosition(): ?int { return $this->position; }
    public function setPosition(int $position): self { $this->position = $position; return $this; }

    public function getIntroText(): ?string { return $this->introText; }
    public function setIntroText(?string $introText): self { $this->introText = $introText; return $this; }

    public function getPrice(): string { return $this->price; }
    public function setPrice(string $price): self { $this->price = $price; return $this; }

    public function __toString(): string
    {
        return $this->title ?? 'Leçon';
    }
}

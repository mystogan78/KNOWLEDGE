<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\Column(length: 180)]
    private string $title;

    #[ORM\Column(length: 180, unique: true)]
    private string $slug;

    // Doctrine retourne des decimals en string → OK
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private string $price; // ex: "50.00"

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** @var Collection<int, Lesson> */
    #[ORM\OneToMany(
        targetEntity: Lesson::class,
        mappedBy: 'course',
        orphanRemoval: true,
        cascade: ['persist']
    )]
    private Collection $lessons;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $introText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getCategory(): Category { return $this->category; }
    public function setCategory(Category $c): self { $this->category = $c; return $this; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $t): self { $this->title = $t; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $s): self { $this->slug = $s; return $this; }

    public function getPrice(): string { return $this->price; }
    public function setPrice(string $p): self { $this->price = $p; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): self { $this->description = $d; return $this; }

    public function getIntroText(): ?string { return $this->introText; }
    public function setIntroText(?string $it): self { $this->introText = $it; return $this; }

    /** @return Collection<int, Lesson> */
    public function getLessons(): Collection { return $this->lessons; }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setCourse($this);
        }
        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            if ($lesson->getCourse() === $this) {
                $lesson->setCourse(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->title ?? 'Cours sans titre';
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}


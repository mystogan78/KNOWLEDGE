<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column(length: 150, unique: true)]
    private string $slug;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $themeColor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $heroText = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $heroVideoUrl = null;

    // Hiérarchie optionnelle : parent / enfants
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
    private ?Category $parent = null;

    /** @var Collection<int, Category> */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $children;

    /** @var Collection<int, Course> */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'category')]
    private Collection $courses;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->courses  = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $n): self { $this->name = $n; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $s): self { $this->slug = $s; return $this; }

    public function getThemeColor(): ?string { return $this->themeColor; }
    public function setThemeColor(?string $c): self { $this->themeColor = $c; return $this; }

    public function getHeroText(): ?string { return $this->heroText; }
    public function setHeroText(?string $t): self { $this->heroText = $t; return $this; }

    public function getHeroVideoUrl(): ?string { return $this->heroVideoUrl; }
    public function setHeroVideoUrl(?string $u): self { $this->heroVideoUrl = $u; return $this; }

    public function getParent(): ?Category { return $this->parent; }
    public function setParent(?Category $parent): self { $this->parent = $parent; return $this; }

    /** @return Collection<int, Category> */
    public function getChildren(): Collection { return $this->children; }

    public function addChild(Category $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        return $this;
    }

    public function removeChild(Category $child): self
    {
        if ($this->children->removeElement($child)) {
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Course> */
    public function getCourses(): Collection { return $this->courses; }

    public function addCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setCategory($this);
        }
        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->courses->removeElement($course)) {
            if ($course->getCategory() === $this) {
                $course->setCategory(null); // attention si nullable=false
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Catégorie';
    }
}

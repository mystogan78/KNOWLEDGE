<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Course;
use App\Entity\Lesson;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[ORM\Table(name: 'purchase')]
#[ORM\UniqueConstraint(name: 'uniq_purchase_session', columns: ['provider_session_id'])]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ✅ L’achat appartient à 1 utilisateur
    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    // ✅ Cible éventuelle : un cours (ou null si c’est une leçon)
    #[ORM\ManyToOne]
    private ?Course $course = null;

    // ✅ Cible éventuelle : une leçon (ou null si c’est un cours)
    #[ORM\ManyToOne]
    private ?Lesson $lesson = null;

    // ✅ Montant payé (euros, ex: "26.00")
    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private string $amount = '0.00';

    #[ORM\Column(length: 10)]
    private string $currency = 'eur';

    // ex: paid, refunded, failed
    #[ORM\Column(length: 20)]
    private string $status = 'paid';

    // ex: stripe
    #[ORM\Column(length: 20)]
    private string $provider = 'stripe';

    // Id de session Stripe – utile pour idempotence
    #[ORM\Column(length: 255)]
    private string $providerSessionId;

    // PaymentIntent (Stripe) optionnel
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $providerPaymentIntentId = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // ❌ PAS de setId() pour un id auto-généré

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;
        return $this;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    public function getProviderSessionId(): string
    {
        return $this->providerSessionId;
    }

    public function setProviderSessionId(string $providerSessionId): self
    {
        $this->providerSessionId = $providerSessionId;
        return $this;
    }

    public function getProviderPaymentIntentId(): ?string
    {
        return $this->providerPaymentIntentId;
    }

    public function setProviderPaymentIntentId(?string $providerPaymentIntentId): self
    {
        $this->providerPaymentIntentId = $providerPaymentIntentId;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}

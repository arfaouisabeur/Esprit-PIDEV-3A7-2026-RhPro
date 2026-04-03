<?php

namespace App\Entity;

use App\Repository\PrimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrimeRepository::class)]
class Prime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'bigint')]
    private int $rhId;
    #[ORM\Column(type: 'bigint')]
    private int $employeId;
    #[ORM\Column(type: 'decimal')]
    private float $montant;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateAttribution;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getRhid(): int
    {
        return $this->rhId;
    }

    public function setRhid(int $rhId): static
    {
        $this->rhId = $rhId;

        return $this;
    }
    public function getEmployeid(): int
    {
        return $this->employeId;
    }

    public function setEmployeid(int $employeId): static
    {
        $this->employeId = $employeId;

        return $this;
    }
    public function getMontant(): float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }
    public function getDateattribution(): \DateTimeInterface
    {
        return $this->dateAttribution;
    }

    public function setDateattribution(\DateTimeInterface $dateAttribution): static
    {
        $this->dateAttribution = $dateAttribution;

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

}

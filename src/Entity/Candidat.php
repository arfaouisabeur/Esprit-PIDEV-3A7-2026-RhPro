<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatRepository::class)]
#[ORM\Table(name: "candidat")]
class Candidat
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?Users $user = null;

    #[ORM\Column(nullable: true)]
    private ?string $niveau_etude = null;

    #[ORM\Column]
    private int $experience;

    // 🔥 IMPORTANT FIX
    public function getId(): ?int
    {
        return $this->user?->getId();
    }

    // ===== GETTERS & SETTERS =====

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(Users $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getNiveauEtude(): ?string
    {
        return $this->niveau_etude;
    }

    public function setNiveauEtude(?string $niveau_etude): static
    {
        $this->niveau_etude = $niveau_etude;
        return $this;
    }

    public function getExperience(): int
    {
        return $this->experience;
    }

    public function setExperience(int $experience): static
    {
        $this->experience = $experience;
        return $this;
    }
    public function __toString(): string
{
    return (string) $this->getId();
}
}
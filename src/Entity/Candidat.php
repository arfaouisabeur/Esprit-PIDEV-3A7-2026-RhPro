<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatRepository::class)]
class Candidat
{
    #[ORM\Id]
    #[ORM\Column(name: 'user_id', type: 'bigint')]
    private ?int $userId = null;

    #[ORM\Column(name: 'niveau_etude', type: 'string', length: 120, nullable: true)]
    private ?string $niveauEtude = null;

    #[ORM\Column(type: 'integer')]
    private ?int $experience = null;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    public function getNiveauEtude(): ?string
    {
        return $this->niveauEtude;
    }

    public function setNiveauEtude(?string $niveauEtude): static
    {
        $this->niveauEtude = $niveauEtude;
        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(int $experience): static
    {
        $this->experience = $experience;
        return $this;
    }
}
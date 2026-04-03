<?php

namespace App\Entity;

use App\Repository\EventParticipationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventParticipationRepository::class)]
class EventParticipation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateInscription;
    #[ORM\Column(type: 'string', length: 60)]
    private string $statut;
    #[ORM\Column(type: 'bigint')]
    private int $evenementId;
    #[ORM\Column(type: 'bigint')]
    private int $employeId;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDateinscription(): \DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateinscription(\DateTimeInterface $dateInscription): static
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }
    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
    public function getEvenementid(): int
    {
        return $this->evenementId;
    }

    public function setEvenementid(int $evenementId): static
    {
        $this->evenementId = $evenementId;

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

}

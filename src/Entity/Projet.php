<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'bigint')]
    private int $rhId;
    #[ORM\Column(type: 'bigint')]
    private int $responsableEmployeId;
    #[ORM\Column(type: 'string', length: 200)]
    private string $titre;
    #[ORM\Column(type: 'string', length: 10)]
    private string $statut;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateDebut;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateFin;

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
    public function getResponsableemployeid(): int
    {
        return $this->responsableEmployeId;
    }

    public function setResponsableemployeid(int $responsableEmployeId): static
    {
        $this->responsableEmployeId = $responsableEmployeId;

        return $this;
    }
    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getDatedebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDatedebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }
    public function getDatefin(): \DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDatefin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

}

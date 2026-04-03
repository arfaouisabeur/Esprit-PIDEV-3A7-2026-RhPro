<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'bigint')]
    private int $projetId;
    #[ORM\Column(type: 'bigint')]
    private int $employeId;
    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $primeId = null;
    #[ORM\Column(type: 'string', length: 200)]
    private string $titre;
    #[ORM\Column(type: 'string', length: 10)]
    private string $statut;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $level = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getProjetid(): int
    {
        return $this->projetId;
    }

    public function setProjetid(int $projetId): static
    {
        $this->projetId = $projetId;

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
    public function getPrimeid(): ?int
    {
        return $this->primeId;
    }

    public function setPrimeid(?int $primeId): static
    {
        $this->primeId = $primeId;

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
    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDatedebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }
    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDatefin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }
    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): static
    {
        $this->level = $level;

        return $this;
    }

}

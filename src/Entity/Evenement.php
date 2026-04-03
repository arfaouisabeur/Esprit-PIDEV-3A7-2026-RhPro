<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'bigint')]
    private int $rhId;
    #[ORM\Column(type: 'string', length: 200)]
    private string $titre;
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateDebut;
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateFin;
    #[ORM\Column(type: 'string', length: 200)]
    private string $lieu;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $imageUrl = null;
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

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
    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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
    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

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
    public function getImageurl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageurl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

}

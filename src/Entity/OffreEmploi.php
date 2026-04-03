<?php

namespace App\Entity;

use App\Repository\OffreEmploiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreEmploiRepository::class)]
class OffreEmploi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 200)]
    private string $titre;
    #[ORM\Column(type: 'text')]
    private string $description;
    #[ORM\Column(type: 'string', length: 200)]
    private string $localisation;
    #[ORM\Column(type: 'string', length: 80)]
    private string $typeContrat;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $datePublication;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateExpiration;
    #[ORM\Column(type: 'string', length: 20)]
    private string $statut;
    #[ORM\Column(type: 'bigint')]
    private int $rhId;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getLocalisation(): string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }
    public function getTypecontrat(): string
    {
        return $this->typeContrat;
    }

    public function setTypecontrat(string $typeContrat): static
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }
    public function getDatepublication(): \DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatepublication(\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }
    public function getDateexpiration(): \DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateexpiration(\DateTimeInterface $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;

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
    public function getRhid(): int
    {
        return $this->rhId;
    }

    public function setRhid(int $rhId): static
    {
        $this->rhId = $rhId;

        return $this;
    }

}

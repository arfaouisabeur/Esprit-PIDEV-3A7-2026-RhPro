<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'bigint')]
    private int $evenementId;
    #[ORM\Column(type: 'bigint')]
    private int $employeId;
    #[ORM\Column(type: 'text')]
    private string $commentaire;
    #[ORM\Column(type: 'integer')]
    private int $etoiles;
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateCreation;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }
    public function getEtoiles(): int
    {
        return $this->etoiles;
    }

    public function setEtoiles(int $etoiles): static
    {
        $this->etoiles = $etoiles;

        return $this;
    }
    public function getDatecreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDatecreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

}

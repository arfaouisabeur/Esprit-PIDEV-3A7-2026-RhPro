<?php

namespace App\Entity;

use App\Repository\SalaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalaireRepository::class)]
class Salaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'bigint')]
    private int $rhId;
    #[ORM\Column(type: 'bigint')]
    private int $employeId;
    #[ORM\Column(type: 'integer')]
    private int $mois;
    #[ORM\Column(type: 'integer')]
    private int $annee;
    #[ORM\Column(type: 'decimal')]
    private float $montant;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $datePaiement = null;
    #[ORM\Column(type: 'string', length: 20)]
    private string $statut;

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
    public function getMois(): int
    {
        return $this->mois;
    }

    public function setMois(int $mois): static
    {
        $this->mois = $mois;

        return $this;
    }
    public function getAnnee(): int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

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
    public function getDatepaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatepaiement(?\DateTimeInterface $datePaiement): static
    {
        $this->datePaiement = $datePaiement;

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

}

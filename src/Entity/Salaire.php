<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SalaireRepository;

#[ORM\Entity(repositoryClass: SalaireRepository::class)]
class Salaire
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private string $mois;

    #[ORM\Column(nullable: false)]
    private string $annee;

    #[ORM\Column(nullable: false)]
    private string $montant;

    #[ORM\Column(nullable: true)]
    private ?string $date_paiement = null;

    #[ORM\Column(nullable: false)]
    private string $statut;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "contract_id", referencedColumnName: "id")]
    private ?Contract $contract = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMois(): ?string
    {
        return $this->mois;
    }

    public function setMois(string $mois): static
    {
        $this->mois = $mois;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): ?string
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(?string $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): static
    {
        $this->contract = $contract;

        return $this;
    }

}

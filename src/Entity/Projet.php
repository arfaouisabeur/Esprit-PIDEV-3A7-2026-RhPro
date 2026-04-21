<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjetRepository;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private string $titre;

    #[ORM\Column(nullable: false)]
    private string $statut;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "rh_id", referencedColumnName: "user_id")]
    private ?Rh $rh = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "responsable_employe_id", referencedColumnName: "user_id")]
    private ?Employe $responsable_employe = null;

    #[ORM\Column(nullable: false)]
    private string $date_debut;

    #[ORM\Column(nullable: false)]
    private string $date_fin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?string
    {
        return $this->date_debut;
    }

    public function setDateDebut(string $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?string
    {
        return $this->date_fin;
    }

    public function setDateFin(string $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getRh(): ?Rh
    {
        return $this->rh;
    }

    public function setRh(?Rh $rh): static
    {
        $this->rh = $rh;

        return $this;
    }

    public function getResponsableEmploye(): ?Employe
    {
        return $this->responsable_employe;
    }

    public function setResponsableEmploye(?Employe $responsable_employe): static
    {
        $this->responsable_employe = $responsable_employe;

        return $this;
    }

}

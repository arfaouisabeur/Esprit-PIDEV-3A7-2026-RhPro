<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\OffreEmploiRepository;

#[ORM\Entity(repositoryClass: OffreEmploiRepository::class)]
#[ORM\Table(name: "offre_emploi")]
class OffreEmploi
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private string $titre;

    #[ORM\Column(nullable: false)]
    private string $description;

    #[ORM\Column(nullable: false)]
    private string $localisation;

    #[ORM\Column(nullable: false)]
    private string $type_contrat;

    #[ORM\Column(nullable: false)]
    private string $date_publication;

    #[ORM\Column(nullable: false)]
    private string $date_expiration;

    #[ORM\Column(nullable: false)]
    private string $statut;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "rh_id", referencedColumnName: "user_id")]
    private ?Rh $rh = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getTypeContrat(): ?string
    {
        return $this->type_contrat;
    }

    public function setTypeContrat(string $type_contrat): static
    {
        $this->type_contrat = $type_contrat;

        return $this;
    }

    public function getDatePublication(): ?string
    {
        return $this->date_publication;
    }

    public function setDatePublication(string $date_publication): static
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getDateExpiration(): ?string
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(string $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

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

    public function getRh(): ?Rh
    {
        return $this->rh;
    }

    public function setRh(?Rh $rh): static
    {
        $this->rh = $rh;

        return $this;
    }

}

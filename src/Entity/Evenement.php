<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\EvenementRepository;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private string $titre;

    #[ORM\Column(nullable: false)]
    private string $date_debut;

    #[ORM\Column(nullable: false)]
    private string $date_fin;

    #[ORM\Column(nullable: false)]
    private string $lieu;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "rh_id", referencedColumnName: "user_id")]
    private ?Rh $rh = null;

    #[ORM\Column(nullable: true)]
    private ?string $image_url = null;

    #[ORM\Column(nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?string $longitude = null;

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

    public function getLieu(): ?string
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

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): static
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

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

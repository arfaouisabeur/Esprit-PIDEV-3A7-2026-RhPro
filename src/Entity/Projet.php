<?php
 
namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjetRepository;
use Symfony\Component\Validator\Constraints as Assert;
use \DateTimeInterface;
 
#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
 
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 
    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank(message: "Le titre du projet est obligatoire.")]
    #[Assert\Regex(
        pattern: "/\d/",
        match: false,
        message: "Le titre du projet ne doit pas contenir de chiffres."
    )]
    private string $titre;
 
    #[ORM\Column(nullable: false)]
    private string $statut;
 
    #[ORM\Column(nullable: true)]
    private ?string $description = null;
 
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "rh_id", referencedColumnName: "user_id", nullable: false)]
    #[Assert\NotBlank(message: "Le responsable RH est obligatoire.")]
    private ?RH $rh = null;
 
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "responsable_employe_id", referencedColumnName: "user_id")]
    private ?Employe $responsable_employe = null;
 
    #[ORM\Column(type: "datetime", nullable: false)]
    #[Assert\NotBlank(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual(
        "today", 
        message: "La date de début doit être aujourd'hui ou une date ultérieure."
    )]
    private ?DateTimeInterface $date_debut = null;

    #[ORM\Column(type: "datetime", nullable: false)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThan(
        propertyPath: "date_debut", 
        message: "La date de fin doit être strictement après la date de début."
    )]
    private ?DateTimeInterface $date_fin = null;
 
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
 
    public function getDateDebut(): ?DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }
 
    public function getRh(): ?RH
    {
        return $this->rh;
    }
 
    public function setRh(?RH $rh): static
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
 
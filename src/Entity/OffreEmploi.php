<?php

namespace App\Entity;

use App\Repository\OffreEmploiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: OffreEmploiRepository::class)]
class OffreEmploi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        max: 255,
        maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères.'
    )]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Assert\NotBlank(message: 'La localisation est obligatoire.')]
    #[ORM\Column(length: 255)]
    private ?string $localisation = null;

    #[Assert\NotBlank(message: 'Le type de contrat est obligatoire.')]
    #[ORM\Column(length: 100)]
    private ?string $typeContrat = null;

    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[ORM\Column(length: 100)]
    private ?string $statut = null;

    #[Assert\NotNull(message: "La date de publication est obligatoire.")]
    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $datePublication = null;

    #[Assert\NotNull(message: "La date d'expiration est obligatoire.")]
    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateExpiration = null;

    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: RH::class)]
    #[ORM\JoinColumn(name: 'rh_id', referencedColumnName: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private ?RH $rh = null;

    #[ORM\OneToMany(mappedBy: 'offreEmploi', targetEntity: Candidature::class, orphanRemoval: true, cascade: ['remove'])]
    private Collection $candidatures;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
    }

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->datePublication && $this->dateExpiration) {
            if ($this->dateExpiration < $this->datePublication) {
                $context->buildViolation("La date d'expiration doit être postérieure à la date de publication.")
                    ->atPath('dateExpiration')
                    ->addViolation();
            }
        }
    }

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
        return $this->typeContrat;
    }

    public function setTypeContrat(string $typeContrat): static
    {
        $this->typeContrat = $typeContrat;
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

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;
        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(\DateTimeInterface $dateExpiration): static
    {
        $this->dateExpiration = $dateExpiration;
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

    public function getRh(): ?RH
    {
        return $this->rh;
    }

    public function setRh(?RH $rh): static
    {
        $this->rh = $rh;
        return $this;
    }

    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): static
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->setOffreEmploi($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): static
    {
        if ($this->candidatures->removeElement($candidature)) {
            if ($candidature->getOffreEmploi() === $this) {
                $candidature->setOffreEmploi(null);
            }
        }

        return $this;
    }
    #[ORM\Column(type: 'float', nullable: true)]
private ?float $latitude = null;

#[ORM\Column(type: 'float', nullable: true)]
private ?float $longitude = null;

public function getLatitude(): ?float { return $this->latitude; }
public function setLatitude(?float $latitude): self { $this->latitude = $latitude; return $this; }

public function getLongitude(): ?float { return $this->longitude; }
public function setLongitude(?float $longitude): self { $this->longitude = $longitude; return $this; }
}

<?php

namespace App\Entity;

use App\Repository\CandidatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
#[ORM\Table(name: 'candidature')]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[Assert\NotNull(message: 'La date de candidature est obligatoire.')]
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateCandidature = null;

    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[ORM\Column(type: 'string', length: 20)]
    private ?string $statut = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $cvPath = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cvOriginalName = null;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $cvSize = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $cvUploadedAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $matchScore = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $matchUpdatedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $cvSkills = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $aiAnalysis = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $signatureRequestId = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $contractStatus = null;

    #[Assert\NotNull(message: 'Le candidat est obligatoire.')]
    #[ORM\ManyToOne(targetEntity: Candidat::class, inversedBy: 'candidatures')]
    #[ORM\JoinColumn(name: 'candidat_id', referencedColumnName: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private ?Candidat $candidat = null;

    #[Assert\NotNull(message: 'L’offre est obligatoire.')]
    #[ORM\ManyToOne(targetEntity: OffreEmploi::class, inversedBy: 'candidatures')]
    #[ORM\JoinColumn(name: 'offre_emploi_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?OffreEmploi $offreEmploi = null;
     // ─── NOUVEAUX CHAMPS ────────────────────────────────────────────────────────

    #[Assert\NotBlank(message: 'La lettre de motivation est obligatoire.')]
    #[Assert\Length(
        min: 50,
        max: 1500,
        minMessage: 'La lettre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La lettre ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $lettreMotivation = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $disponibilite = null;

    #[Assert\Positive(message: 'La prétention salariale doit être un nombre positif.')]
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $pretentionSalariale = null;

    // ─── GETTERS / SETTERS ──────────────────────────────────────────────────────

    public function getLettreMotivation(): ?string
    {
        return $this->lettreMotivation;
    }

    public function setLettreMotivation(?string $lettreMotivation): static
    {
        $this->lettreMotivation = $lettreMotivation;
        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(?string $disponibilite): static
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    public function getPretentionSalariale(): ?int
    {
        return $this->pretentionSalariale;
    }

    public function setPretentionSalariale(?int $pretentionSalariale): static
    {
        $this->pretentionSalariale = $pretentionSalariale;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCandidature(): ?\DateTimeInterface
    {
        return $this->dateCandidature;
    }

    public function setDateCandidature(?\DateTimeInterface $dateCandidature): static
    {
        $this->dateCandidature = $dateCandidature;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCvPath(): ?string
    {
        return $this->cvPath;
    }

    public function setCvPath(?string $cvPath): static
    {
        $this->cvPath = $cvPath;
        return $this;
    }

    public function getCvOriginalName(): ?string
    {
        return $this->cvOriginalName;
    }

    public function setCvOriginalName(?string $cvOriginalName): static
    {
        $this->cvOriginalName = $cvOriginalName;
        return $this;
    }

    public function getCvSize(): ?int
    {
        return $this->cvSize;
    }

    public function setCvSize(?int $cvSize): static
    {
        $this->cvSize = $cvSize;
        return $this;
    }

    public function getCvUploadedAt(): ?\DateTimeInterface
    {
        return $this->cvUploadedAt;
    }

    public function setCvUploadedAt(?\DateTimeInterface $cvUploadedAt): static
    {
        $this->cvUploadedAt = $cvUploadedAt;
        return $this;
    }

    public function getMatchScore(): ?int
    {
        return $this->matchScore;
    }

    public function setMatchScore(?int $matchScore): static
    {
        $this->matchScore = $matchScore;
        return $this;
    }

    public function getMatchUpdatedAt(): ?\DateTimeInterface
    {
        return $this->matchUpdatedAt;
    }

    public function setMatchUpdatedAt(?\DateTimeInterface $matchUpdatedAt): static
    {
        $this->matchUpdatedAt = $matchUpdatedAt;
        return $this;
    }

    public function getCvSkills(): ?string
    {
        return $this->cvSkills;
    }

    public function setCvSkills(?string $cvSkills): static
    {
        $this->cvSkills = $cvSkills;
        return $this;
    }

    public function getAiAnalysis(): ?string
    {
        return $this->aiAnalysis;
    }

    public function setAiAnalysis(?string $aiAnalysis): static
    {
        $this->aiAnalysis = $aiAnalysis;
        return $this;
    }

    public function getSignatureRequestId(): ?string
    {
        return $this->signatureRequestId;
    }

    public function setSignatureRequestId(?string $signatureRequestId): static
    {
        $this->signatureRequestId = $signatureRequestId;
        return $this;
    }

    public function getContractStatus(): ?string
    {
        return $this->contractStatus;
    }

    public function setContractStatus(?string $contractStatus): static
    {
        $this->contractStatus = $contractStatus;
        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): static
    {
        $this->candidat = $candidat;
        return $this;
    }

    public function getOffreEmploi(): ?OffreEmploi
    {
        return $this->offreEmploi;
    }

    public function setOffreEmploi(?OffreEmploi $offreEmploi): static
    {
        $this->offreEmploi = $offreEmploi;
        return $this;
    }
}

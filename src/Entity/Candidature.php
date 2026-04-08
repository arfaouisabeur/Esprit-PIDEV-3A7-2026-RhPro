<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CandidatureRepository;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_candidature;

    #[ORM\Column(length: 20)]
    private string $statut;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $cv_path = null;

    // ✅ FIX FK (IMPORTANT)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "candidat_id", referencedColumnName: "user_id", nullable: false)]
    private ?Candidat $candidat = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "offre_emploi_id", referencedColumnName: "id", nullable: false)]
    private ?OffreEmploi $offre_emploi = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cv_original_name = null;

    // ✅ FIX TYPE
    #[ORM\Column(type: "bigint", nullable: true)]
    private ?int $cv_size = null;

    // ✅ FIX TYPE
    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $cv_uploaded_at = null;

    #[ORM\Column(nullable: true)]
    private ?int $match_score = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $match_updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signature_request_id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $contract_status = null;

    // ===== GETTERS & SETTERS =====

    public function getId(): ?int { return $this->id; }

    public function getDateCandidature(): ?\DateTimeInterface { return $this->date_candidature; }
    public function setDateCandidature(\DateTimeInterface $date): static { $this->date_candidature = $date; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getCvPath(): ?string { return $this->cv_path; }
    public function setCvPath(?string $cv_path): static { $this->cv_path = $cv_path; return $this; }

    public function getCvOriginalName(): ?string { return $this->cv_original_name; }
    public function setCvOriginalName(?string $name): static { $this->cv_original_name = $name; return $this; }

    public function getCvSize(): ?int { return $this->cv_size; }
    public function setCvSize(?int $size): static { $this->cv_size = $size; return $this; }

    public function getCvUploadedAt(): ?\DateTimeInterface { return $this->cv_uploaded_at; }
    public function setCvUploadedAt(?\DateTimeInterface $date): static { $this->cv_uploaded_at = $date; return $this; }

    public function getMatchScore(): ?int { return $this->match_score; }
    public function setMatchScore(?int $score): static { $this->match_score = $score; return $this; }

    public function getMatchUpdatedAt(): ?\DateTimeInterface { return $this->match_updated_at; }
    public function setMatchUpdatedAt(?\DateTimeInterface $date): static { $this->match_updated_at = $date; return $this; }

    public function getSignatureRequestId(): ?string { return $this->signature_request_id; }
    public function setSignatureRequestId(?string $id): static { $this->signature_request_id = $id; return $this; }

    public function getContractStatus(): ?string { return $this->contract_status; }
    public function setContractStatus(?string $status): static { $this->contract_status = $status; return $this; }

    public function getCandidat(): ?Candidat { return $this->candidat; }
    public function setCandidat(Candidat $candidat): static { $this->candidat = $candidat; return $this; }

    public function getOffreEmploi(): ?OffreEmploi { return $this->offre_emploi; }
    public function setOffreEmploi(OffreEmploi $offre): static { $this->offre_emploi = $offre; return $this; }
}
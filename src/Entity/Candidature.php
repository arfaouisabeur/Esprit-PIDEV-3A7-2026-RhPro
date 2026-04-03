<?php

namespace App\Entity;

use App\Repository\CandidatureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatureRepository::class)]
class Candidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateCandidature;
    #[ORM\Column(type: 'string', length: 20)]
    private string $statut;
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $cvPath = null;
    #[ORM\Column(type: 'bigint')]
    private int $candidatId;
    #[ORM\Column(type: 'bigint')]
    private int $offreEmploiId;
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
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $signatureRequestId = null;
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $contractStatus = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDatecandidature(): \DateTimeInterface
    {
        return $this->dateCandidature;
    }

    public function setDatecandidature(\DateTimeInterface $dateCandidature): static
    {
        $this->dateCandidature = $dateCandidature;

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
    public function getCvpath(): ?string
    {
        return $this->cvPath;
    }

    public function setCvpath(?string $cvPath): static
    {
        $this->cvPath = $cvPath;

        return $this;
    }
    public function getCandidatid(): int
    {
        return $this->candidatId;
    }

    public function setCandidatid(int $candidatId): static
    {
        $this->candidatId = $candidatId;

        return $this;
    }
    public function getOffreemploiid(): int
    {
        return $this->offreEmploiId;
    }

    public function setOffreemploiid(int $offreEmploiId): static
    {
        $this->offreEmploiId = $offreEmploiId;

        return $this;
    }
    public function getCvoriginalname(): ?string
    {
        return $this->cvOriginalName;
    }

    public function setCvoriginalname(?string $cvOriginalName): static
    {
        $this->cvOriginalName = $cvOriginalName;

        return $this;
    }
    public function getCvsize(): ?int
    {
        return $this->cvSize;
    }

    public function setCvsize(?int $cvSize): static
    {
        $this->cvSize = $cvSize;

        return $this;
    }
    public function getCvuploadedat(): ?\DateTimeInterface
    {
        return $this->cvUploadedAt;
    }

    public function setCvuploadedat(?\DateTimeInterface $cvUploadedAt): static
    {
        $this->cvUploadedAt = $cvUploadedAt;

        return $this;
    }
    public function getMatchscore(): ?int
    {
        return $this->matchScore;
    }

    public function setMatchscore(?int $matchScore): static
    {
        $this->matchScore = $matchScore;

        return $this;
    }
    public function getMatchupdatedat(): ?\DateTimeInterface
    {
        return $this->matchUpdatedAt;
    }

    public function setMatchupdatedat(?\DateTimeInterface $matchUpdatedAt): static
    {
        $this->matchUpdatedAt = $matchUpdatedAt;

        return $this;
    }
    public function getSignaturerequestid(): ?string
    {
        return $this->signatureRequestId;
    }

    public function setSignaturerequestid(?string $signatureRequestId): static
    {
        $this->signatureRequestId = $signatureRequestId;

        return $this;
    }
    public function getContractstatus(): ?string
    {
        return $this->contractStatus;
    }

    public function setContractstatus(?string $contractStatus): static
    {
        $this->contractStatus = $contractStatus;

        return $this;
    }

}

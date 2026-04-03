<?php

namespace App\Entity;

use App\Repository\CongeTtRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CongeTtRepository::class)]
class CongeTt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'bigint')]
    private int $employeId;
    #[ORM\Column(type: 'string', length: 255)]
    private string $typeConge;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateDebut;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateFin;
    #[ORM\Column(type: 'string', length: 255)]
    private string $statut;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $documentPath = null;
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $ocrVerified = null;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getTypeconge(): string
    {
        return $this->typeConge;
    }

    public function setTypeconge(string $typeConge): static
    {
        $this->typeConge = $typeConge;

        return $this;
    }
    public function getDatedebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDatedebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }
    public function getDatefin(): \DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDatefin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getDocumentpath(): ?string
    {
        return $this->documentPath;
    }

    public function setDocumentpath(?string $documentPath): static
    {
        $this->documentPath = $documentPath;

        return $this;
    }
    public function getOcrverified(): ?bool
    {
        return $this->ocrVerified;
    }

    public function setOcrverified(?bool $ocrVerified): static
    {
        $this->ocrVerified = $ocrVerified;

        return $this;
    }

}

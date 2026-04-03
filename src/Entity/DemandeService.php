<?php

namespace App\Entity;

use App\Repository\DemandeServiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeServiceRepository::class)]
class DemandeService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'bigint')]
    private int $employeId;
    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $typeServiceId = null;
    #[ORM\Column(type: 'string', length: 200)]
    private string $titre;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateDemande;
    #[ORM\Column(type: 'string', length: 20)]
    private string $statut;
    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private ?string $etapeWorkflow = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateDerniereEtape = null;
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $priorite = null;
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $deadlineReponse = null;
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $slaDepasse = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pdfPath = null;

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
    public function getTypeserviceid(): ?int
    {
        return $this->typeServiceId;
    }

    public function setTypeserviceid(?int $typeServiceId): static
    {
        $this->typeServiceId = $typeServiceId;

        return $this;
    }
    public function getTitre(): string
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getDatedemande(): \DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDatedemande(\DateTimeInterface $dateDemande): static
    {
        $this->dateDemande = $dateDemande;

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
    public function getEtapeworkflow(): ?string
    {
        return $this->etapeWorkflow;
    }

    public function setEtapeworkflow(?string $etapeWorkflow): static
    {
        $this->etapeWorkflow = $etapeWorkflow;

        return $this;
    }
    public function getDatederniereetape(): ?\DateTimeInterface
    {
        return $this->dateDerniereEtape;
    }

    public function setDatederniereetape(?\DateTimeInterface $dateDerniereEtape): static
    {
        $this->dateDerniereEtape = $dateDerniereEtape;

        return $this;
    }
    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(?string $priorite): static
    {
        $this->priorite = $priorite;

        return $this;
    }
    public function getDeadlinereponse(): ?\DateTimeInterface
    {
        return $this->deadlineReponse;
    }

    public function setDeadlinereponse(?\DateTimeInterface $deadlineReponse): static
    {
        $this->deadlineReponse = $deadlineReponse;

        return $this;
    }
    public function getSladepasse(): ?bool
    {
        return $this->slaDepasse;
    }

    public function setSladepasse(?bool $slaDepasse): static
    {
        $this->slaDepasse = $slaDepasse;

        return $this;
    }
    public function getPdfpath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfpath(?string $pdfPath): static
    {
        $this->pdfPath = $pdfPath;

        return $this;
    }

}

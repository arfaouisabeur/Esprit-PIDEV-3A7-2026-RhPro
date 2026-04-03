<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 60)]
    private string $decision;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $commentaire = null;
    #[ORM\Column(type: 'bigint')]
    private int $rhId;
    #[ORM\Column(type: 'bigint')]
    private int $employeId;
    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $congeTtId = null;
    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $demandeServiceId = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDecision(): string
    {
        return $this->decision;
    }

    public function setDecision(string $decision): static
    {
        $this->decision = $decision;

        return $this;
    }
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }
    public function getRhid(): int
    {
        return $this->rhId;
    }

    public function setRhid(int $rhId): static
    {
        $this->rhId = $rhId;

        return $this;
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
    public function getCongettid(): ?int
    {
        return $this->congeTtId;
    }

    public function setCongettid(?int $congeTtId): static
    {
        $this->congeTtId = $congeTtId;

        return $this;
    }
    public function getDemandeserviceid(): ?int
    {
        return $this->demandeServiceId;
    }

    public function setDemandeserviceid(?int $demandeServiceId): static
    {
        $this->demandeServiceId = $demandeServiceId;

        return $this;
    }

}

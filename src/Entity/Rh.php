<?php

namespace App\Entity;

use App\Repository\RhRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RhRepository::class)]
#[ORM\Table(name: "rh")]
class Rh
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?Users $user = null;

    // 🔥 CRITICAL FIX
    public function getId(): ?int
    {
        return $this->user?->getId();
    }

    // ===== GETTERS & SETTERS =====

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(Users $user): static
    {
        $this->user = $user;
        return $this;
    }
}
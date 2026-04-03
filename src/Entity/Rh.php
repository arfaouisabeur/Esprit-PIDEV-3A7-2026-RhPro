<?php

namespace App\Entity;

use App\Repository\RhRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RhRepository::class)]
class Rh
{
    #[ORM\Id]
    #[ORM\Column(name: 'user_id', type: 'bigint')]
    private ?int $userId = null;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;
        return $this;
    }
}
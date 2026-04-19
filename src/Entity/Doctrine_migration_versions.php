<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Doctrine_migration_versions
{

    #[ORM\Id]
    #[ORM\Column]
    private ?int $version = null;

    #[ORM\Column(nullable: true)]
    private ?string $executed_at = null;

    #[ORM\Column(nullable: true)]
    private ?string $execution_time = null;

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function getExecutedAt(): ?string
    {
        return $this->executed_at;
    }

    public function setExecutedAt(?string $executed_at): static
    {
        $this->executed_at = $executed_at;

        return $this;
    }

    public function getExecutionTime(): ?string
    {
        return $this->execution_time;
    }

    public function setExecutionTime(?string $execution_time): static
    {
        $this->execution_time = $execution_time;

        return $this;
    }

}

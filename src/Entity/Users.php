<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 120)]
    private string $nom;
    #[ORM\Column(type: 'string', length: 120)]
    private string $prenom;
    #[ORM\Column(type: 'string', length: 255)]
    private string $email;
    #[ORM\Column(type: 'string', length: 255)]
    private string $motDePasse;
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private ?string $telephone = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adresse = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $role = null;
    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $avatarPath = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    public function getMotdepasse(): string
    {
        return $this->motDePasse;
    }

    public function setMotdepasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }
    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }
    public function getAvatarpath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarpath(?string $avatarPath): static
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

}

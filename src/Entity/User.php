<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_CANDIDAT = 'CANDIDAT';
    public const ROLE_EMPLOYE  = 'EMPLOYE';
    public const ROLE_RH       = 'RH';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 120)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 120)]
    private ?string $prenom = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $avatarPath = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $statut = 'actif';

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Candidat::class, cascade: ['persist', 'remove'])]
    private ?Candidat $candidat = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Employe::class, cascade: ['persist', 'remove'])]
    private ?Employe $employe = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: RH::class, cascade: ['persist', 'remove'])]
    private ?RH $rh = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->role) {
            $roles[] = 'ROLE_' . strtoupper($this->role);
        }
        return array_unique($roles);
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(?string $avatarPath): self
    {
        $this->avatarPath = $avatarPath;
        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;
        if ($candidat !== null && $candidat->getUser() !== $this) {
            $candidat->setUser($this);
        }
        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;
        if ($employe !== null && $employe->getUser() !== $this) {
            $employe->setUser($this);
        }
        return $this;
    }

    public function getRh(): ?RH
    {
        return $this->rh;
    }

    public function setRh(?RH $rh): self
    {
        $this->rh = $rh;
        if ($rh !== null && $rh->getUser() !== $this) {
            $rh->setUser($this);
        }
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function isCandidat(): bool
    {
        return $this->role === self::ROLE_CANDIDAT;
    }

    public function isEmploye(): bool
    {
        return $this->role === self::ROLE_EMPLOYE;
    }

    public function isRH(): bool
    {
        return $this->role === self::ROLE_RH;
    }
}
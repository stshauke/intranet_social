<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Importation du repository et des composants nécessaires
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

// Déclaration de l'entité User avec son repository personnalisé
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks] // Permet l'exécution automatique des méthodes annotées @PrePersist / @PreUpdate
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Identifiant unique de l'utilisateur
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Adresse email de l'utilisateur (doit être unique)
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    // Tableau des rôles de l'utilisateur (stocké en JSON)
    #[ORM\Column]
    private array $roles = [];

    // Mot de passe hashé
    #[ORM\Column]
    private ?string $password = null;

    // Nom complet de l'utilisateur
    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    // Nom du fichier de l'image de profil (optionnel)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    // Biographie de l'utilisateur (optionnelle, type TEXT)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    // Date de création du compte
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Date de dernière mise à jour du compte
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour l'email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Setter pour l'email
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Identifiant unique utilisé pour l'authentification
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    // Retourne les rôles, en garantissant toujours au moins 'ROLE_USER'
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    // Setter pour les rôles
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Getter pour le mot de passe
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Setter pour le mot de passe
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Méthode obligatoire pour l'interface UserInterface (utile si on stocke des infos temporaires)
    public function eraseCredentials(): void
    {
        // Si tu stockes des données temporaires sensibles sur l'utilisateur, vide-les ici
    }

    // Getter pour le nom complet
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    // Setter pour le nom complet
    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    // Getter pour l'image de profil
    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    // Setter pour l'image de profil
    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;
        return $this;
    }

    // Getter pour la bio
    public function getBio(): ?string
    {
        return $this->bio;
    }

    // Setter pour la bio
    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    // Getter pour la date de création
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setter pour la date de création
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Getter pour la date de mise à jour
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    // Setter pour la date de mise à jour
    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Callback exécuté automatiquement avant la première insertion en base
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
    }

    // Callback exécuté automatiquement avant chaque mise à jour
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }
}

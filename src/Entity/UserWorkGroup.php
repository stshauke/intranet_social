<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Importation du repository personnalisé et des annotations ORM de Doctrine
use App\Repository\UserWorkGroupRepository;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité UserWorkGroup avec son repository
#[ORM\Entity(repositoryClass: UserWorkGroupRepository::class)]
#[ORM\HasLifecycleCallbacks] // Permet d'utiliser des méthodes comme @PrePersist
class UserWorkGroup
{
    // Identifiant unique de l'entrée (clé primaire auto-générée)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Relation vers l'entité User (utilisateur membre du groupe)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)] // Relation obligatoire
    private ?User $user = null;

    // Relation vers l'entité WorkGroup (le groupe auquel appartient l'utilisateur)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)] // Relation obligatoire
    private ?WorkGroup $workGroup = null;

    // Détermine si l'utilisateur est administrateur du groupe
    #[ORM\Column]
    private ?bool $isAdmin = null;

    // Date à laquelle l'utilisateur a rejoint le groupe
    #[ORM\Column]
    private ?\DateTimeImmutable $joinedAt = null;

    // Callback exécuté automatiquement avant insertion : initialise la date d’adhésion
    #[ORM\PrePersist]
    public function setTimestamps(): void
    {
        $this->joinedAt = new \DateTimeImmutable();
    }

    // Getter pour l'identifiant
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour l'utilisateur
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Setter pour l'utilisateur
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Getter pour le groupe de travail
    public function getWorkGroup(): ?WorkGroup
    {
        return $this->workGroup;
    }

    // Setter pour le groupe de travail
    public function setWorkGroup(?WorkGroup $workGroup): static
    {
        $this->workGroup = $workGroup;
        return $this;
    }

    // Getter pour le statut d'administrateur
    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    // Setter pour le statut d'administrateur
    public function setIsAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    // Getter pour la date d'adhésion
    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joinedAt;
    }

    // Setter pour la date d'adhésion
    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;
        return $this;
    }
}

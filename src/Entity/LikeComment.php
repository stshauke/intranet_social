<?php

// Déclaration du namespace
namespace App\Entity;

// Importation du repository associé et des annotations ORM de Doctrine
use App\Repository\LikeCommentRepository;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité LikeComment avec son repository personnalisé
#[ORM\Entity(repositoryClass: LikeCommentRepository::class)]
#[ORM\HasLifecycleCallbacks] // Permet d’utiliser des méthodes comme @PrePersist
class LikeComment
{
    // Identifiant unique du like (clé primaire auto-générée)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Utilisateur ayant liké le commentaire (relation obligatoire)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Commentaire qui a été liké (relation obligatoire)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comment $comment = null;

    // Date de création du like (automatiquement définie à la création)
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Callback exécuté automatiquement avant l'insertion en base de données
    #[ORM\PrePersist]
    public function setTimestamps(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getter pour l'identifiant
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour l'utilisateur ayant liké
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

    // Getter pour le commentaire liké
    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    // Setter pour le commentaire
    public function setComment(?Comment $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    // Getter pour la date de création
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setter pour la date de création (utile pour modification manuelle si nécessaire)
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}

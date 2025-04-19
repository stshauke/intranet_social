<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Importation du repository associé et des annotations Doctrine ORM
use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité Like avec son repository et table personnalisée
#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\HasLifecycleCallbacks]                      // Permet d'utiliser des callbacks comme @PrePersist
#[ORM\Table(name: '`post_like`')]                 // Nom personnalisé de la table (protégé avec `backticks`)
class Like
{
    // Clé primaire auto-générée
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // L'utilisateur qui a aimé la publication (relation ManyToOne obligatoire)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // La publication qui a été aimée (relation ManyToOne obligatoire)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    // Date et heure à laquelle le like a été créé
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Callback exécuté automatiquement avant insertion pour définir la date de création
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

    // Getter pour le post
    public function getPost(): ?Post
    {
        return $this->post;
    }

    // Setter pour le post
    public function setPost(?Post $post): static
    {
        $this->post = $post;
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
}

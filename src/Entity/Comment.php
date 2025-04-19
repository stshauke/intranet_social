<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Importation du repository et des outils de mapping ORM
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité Comment avec son repository associé
#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks] // Active les callbacks comme @PrePersist
class Comment
{
    // Identifiant unique du commentaire (clé primaire auto-incrémentée)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Contenu du commentaire (champ de type texte)
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    // Date et heure de création du commentaire
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Relation ManyToOne : un commentaire appartient à un utilisateur
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)] // Champ obligatoire
    private ?User $user = null;

    // Relation ManyToOne : un commentaire appartient à un post
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)] // Champ obligatoire
    private ?Post $post = null;

    // Callback exécuté automatiquement avant la persistance (insertion)
    #[ORM\PrePersist]
    public function setTimestamps(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour le contenu du commentaire
    public function getContent(): ?string
    {
        return $this->content;
    }

    // Setter pour le contenu du commentaire
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    // Getter pour la date de création
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setter pour la date de création (utile en cas de modification manuelle)
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Getter pour l'utilisateur auteur du commentaire
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

    // Getter pour le post associé
    public function getPost(): ?Post
    {
        return $this->post;
    }

    // Setter pour le post associé
    public function setPost(?Post $post): static
    {
        $this->post = $post;
        return $this;
    }
}

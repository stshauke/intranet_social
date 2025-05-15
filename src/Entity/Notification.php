<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Importation du repository et des annotations ORM de Doctrine
use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de la classe Notification comme entité Doctrine
#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\HasLifecycleCallbacks] // Permet d'exécuter automatiquement des méthodes comme @PrePersist
class Notification
{
    // Clé primaire auto-incrémentée
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Type de notification (ex : new_comment, mention, etc.)
    #[ORM\Column(length: 50)]
    private ?string $type = null;

    // Message descriptif de la notification
    #[ORM\Column(length: 255)]
    private ?string $message = null;

    // Statut de lecture : true = lue, false = non lue
    #[ORM\Column]
    private ?bool $isRead = null;

    // Date et heure de création de la notification
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Utilisateur auquel est destinée la notification (obligatoire)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Post éventuellement lié à la notification (optionnel)
    //#[ORM\ManyToOne]
    //private ?Post $relatedPost = null;
    #[ORM\ManyToOne(targetEntity: Post::class, cascade: ['persist'])]
    private ?Post $relatedPost = null;


    // Commentaire éventuellement lié à la notification (optionnel)
    #[ORM\ManyToOne]
    private ?Comment $relatedComment = null;

    // Callback exécuté avant insertion en base pour définir la date de création
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

    // Getter pour le type de notification
    public function getType(): ?string
    {
        return $this->type;
    }

    // Setter pour le type de notification
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    // Getter pour le message de notification
    public function getMessage(): ?string
    {
        return $this->message;
    }

    // Setter pour le message de notification
    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    // Getter pour le statut de lecture
    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    // Setter pour le statut de lecture
    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;
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

    // Getter pour l'utilisateur destinataire
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Setter pour l'utilisateur destinataire
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Getter pour le post lié (si applicable)
    public function getRelatedPost(): ?Post
    {
        return $this->relatedPost;
    }

    // Setter pour le post lié
    public function setRelatedPost(?Post $relatedPost): static
    {
        $this->relatedPost = $relatedPost;
        return $this;
    }

    // Getter pour le commentaire lié (si applicable)
    public function getRelatedComment(): ?Comment
    {
        return $this->relatedComment;
    }

    // Setter pour le commentaire lié
    public function setRelatedComment(?Comment $relatedComment): static
    {
        $this->relatedComment = $relatedComment;
        return $this;
    }
    #[ORM\ManyToOne]
private ?Message $relatedMessage = null;

public function getRelatedMessage(): ?Message
{
    return $this->relatedMessage;
}

public function setRelatedMessage(?Message $relatedMessage): static
{
    $this->relatedMessage = $relatedMessage;
    return $this;
}

}

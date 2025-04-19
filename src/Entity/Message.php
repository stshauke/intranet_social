<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Importation du repository personnalisé et des annotations ORM
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité Message et association à son repository
#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks] // Active les callbacks comme @PrePersist
class Message
{
    // Identifiant unique du message (clé primaire auto-générée)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Contenu du message (type texte long)
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    // Date et heure de création du message
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Statut de lecture du message (true = lu, false = non lu)
    #[ORM\Column]
    private ?bool $isRead = null;

    // Relation vers l'utilisateur expéditeur du message
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    // Relation vers l'utilisateur destinataire du message
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;

    // Callback exécuté automatiquement avant la persistance pour définir la date de création
    #[ORM\PrePersist]
    public function setTimestamps(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getter pour l'ID du message
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour le contenu du message
    public function getContent(): ?string
    {
        return $this->content;
    }

    // Setter pour le contenu du message
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

    // Setter pour la date de création
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Getter pour savoir si le message a été lu
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

    // Getter pour l'utilisateur expéditeur
    public function getSender(): ?User
    {
        return $this->sender;
    }

    // Setter pour l'utilisateur expéditeur
    public function setSender(?User $sender): static
    {
        $this->sender = $sender;
        return $this;
    }

    // Getter pour l'utilisateur destinataire
    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    // Setter pour l'utilisateur destinataire
    public function setRecipient(?User $recipient): static
    {
        $this->recipient = $recipient;
        return $this;
    }
}

<?php

// Déclaration du namespace
namespace App\Entity;

// Importation du repository associé à l'entité
use App\Repository\GroupMessageRepository;

// Importation des annotations ORM de Doctrine
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité GroupMessage avec son repository personnalisé
#[ORM\Entity(repositoryClass: GroupMessageRepository::class)]
#[ORM\HasLifecycleCallbacks] // Permet d'exécuter des méthodes automatiquement (comme @PrePersist)
class GroupMessage
{
    // Clé primaire auto-incrémentée
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Contenu du message (texte long)
    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    // Relation ManyToOne vers l'utilisateur auteur du message
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)] // Champ obligatoire
    private ?User $author = null;

    // Relation ManyToOne vers le groupe de travail auquel le message appartient
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)] // Champ obligatoire
    private ?WorkGroup $workGroup = null;

    // Date de création du message
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Callback exécuté automatiquement avant l'insertion en base de données
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getter pour l'identifiant
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
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    // Getter pour l'auteur du message
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    // Setter pour l'auteur du message
    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }

    // Getter pour le groupe de travail
    public function getWorkGroup(): ?WorkGroup
    {
        return $this->workGroup;
    }

    // Setter pour le groupe de travail
    public function setWorkGroup(WorkGroup $workGroup): self
    {
        $this->workGroup = $workGroup;
        return $this;
    }

    // Getter pour la date de création
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Setter pour la date de création
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}

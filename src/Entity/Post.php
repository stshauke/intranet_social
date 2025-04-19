<?php

// Déclaration du namespace
namespace App\Entity;

// Importation du repository associé et des classes Doctrine utiles
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité Post avec son repository
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\HasLifecycleCallbacks] // Permet d'utiliser des méthodes comme @PrePersist ou @PreUpdate
class Post
{
    // Identifiant unique du post (clé primaire)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Titre du post
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // Contenu textuel du post (long texte)
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    // Date de création (non modifiable)
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Date de dernière mise à jour (modifiable)
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    // Utilisateur auteur du post
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Liste des likes associés au post
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Like::class, cascade: ['remove'])]
    private Collection $likes;

    // Liste des commentaires associés au post
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, cascade: ['remove'])]
    private Collection $comments;

    // Liste des pièces jointes associées au post
    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Attachment::class, cascade: ['persist', 'remove'])]
    private Collection $attachments;

    // Groupe de travail auquel le post appartient (optionnel)
    #[ORM\ManyToOne]
    private ?WorkGroup $workGroup = null;

    // Constructeur : initialise les collections
    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->attachments = new ArrayCollection();
    }

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour le titre
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Setter pour le titre
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    // Getter pour le contenu
    public function getContent(): ?string
    {
        return $this->content;
    }

    // Setter pour le contenu
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

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    // Ajoute un like au post
    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setPost($this);
        }

        return $this;
    }

    // Supprime un like du post
    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    // Compte le nombre de likes
    public function getLikeCount(): int
    {
        return count($this->likes);
    }

    /**
     * ✅✅✅ Méthode manquante que tu avais besoin ✅✅✅
     * Vérifie si un utilisateur donné a liké ce post
     */
    public function isLikedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    // Ajoute un commentaire au post
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    // Supprime un commentaire du post
    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    // Ajoute une pièce jointe au post
    public function addAttachment(Attachment $attachment): static
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
            $attachment->setPost($this);
        }

        return $this;
    }

    // Supprime une pièce jointe du post
    public function removeAttachment(Attachment $attachment): static
    {
        if ($this->attachments->removeElement($attachment)) {
            if ($attachment->getPost() === $this) {
                $attachment->setPost(null);
            }
        }

        return $this;
    }

    // Getter pour le groupe de travail associé
    public function getWorkGroup(): ?WorkGroup
    {
        return $this->workGroup;
    }

    // Setter pour le groupe de travail associé
    public function setWorkGroup(?WorkGroup $workGroup): static
    {
        $this->workGroup = $workGroup;

        return $this;
    }
}

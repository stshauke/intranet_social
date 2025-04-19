<?php

// Déclaration du namespace de l'entité
namespace App\Entity;

// Importation des classes nécessaires
use App\Repository\WorkGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// Déclaration de l'entité WorkGroup avec son repository
#[ORM\Entity(repositoryClass: WorkGroupRepository::class)]
#[ORM\HasLifecycleCallbacks] // Permet d'exécuter des méthodes automatiquement (comme @PreUpdate)
class WorkGroup
{
    // Identifiant unique du groupe de travail (clé primaire)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Nom du groupe
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    // Description du groupe
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    // Indique si le groupe est privé (true/false)
    #[ORM\Column]
    private ?bool $isPrivate = false;

    // Créateur du groupe (relation vers User)
    #[ORM\ManyToOne(inversedBy: 'createdGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    // Liste des membres du groupe (relation ManyToMany avec User)
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'workGroups')]
    private Collection $members;

    // Liste des posts associés au groupe (relation OneToMany)
    #[ORM\OneToMany(mappedBy: 'workGroup', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    // Liste des messages associés au groupe (relation OneToMany)
    #[ORM\OneToMany(mappedBy: 'workGroup', targetEntity: GroupMessage::class, orphanRemoval: true)]
    private Collection $messages;

    // Date de création du groupe
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    // Date de dernière mise à jour du groupe
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // Constructeur : initialise les collections et les dates
    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // GETTERS & SETTERS

    // ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Name
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // Description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    // IsPrivate
    public function isPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    // Creator
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;
        return $this;
    }

    // Members
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);
        return $this;
    }

    // ✅ Posts
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setWorkGroup($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getWorkGroup() === $this) {
                $post->setWorkGroup(null);
            }
        }

        return $this;
    }

    // ✅ Messages
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(GroupMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setWorkGroup($this);
        }

        return $this;
    }

    public function removeMessage(GroupMessage $message): self
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getWorkGroup() === $this) {
                $message->setWorkGroup(null);
            }
        }

        return $this;
    }

    // Dates
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Callback exécuté automatiquement avant la mise à jour pour mettre à jour la date
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}

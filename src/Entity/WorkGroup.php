<?php

namespace App\Entity;

use App\Repository\WorkGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkGroupRepository::class)]
#[ORM\HasLifecycleCallbacks]
class WorkGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $type = 'public';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $moderators;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'workGroup', targetEntity: UserWorkGroup::class, cascade: ['persist', 'remove'])]
    private Collection $userLinks;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: FavoriteGroup::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $favoriteGroups;

    public function __construct()
    {
        $this->moderators = new ArrayCollection();
        $this->userLinks = new ArrayCollection();
        $this->favoriteGroups = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): ?string { return $this->type; }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCreator(): ?User { return $this->creator; }

    public function setCreator(User $creator): self
    {
        $this->creator = $creator;
        return $this;
    }

    public function getModerators(): Collection { return $this->moderators; }

    public function addModerator(User $moderator): self
    {
        if (!$this->moderators->contains($moderator)) {
            $this->moderators[] = $moderator;
        }
        return $this;
    }

    public function removeModerator(User $moderator): self
    {
        $this->moderators->removeElement($moderator);
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getUserLinks(): Collection
    {
        return $this->userLinks;
    }

    public function getFavoriteGroups(): Collection
    {
        return $this->favoriteGroups;
    }

    public function addFavoriteGroup(FavoriteGroup $favoriteGroup): self
    {
        if (!$this->favoriteGroups->contains($favoriteGroup)) {
            $this->favoriteGroups[] = $favoriteGroup;
            $favoriteGroup->setGroup($this);
        }

        return $this;
    }

    public function removeFavoriteGroup(FavoriteGroup $favoriteGroup): self
    {
        if ($this->favoriteGroups->removeElement($favoriteGroup)) {
            if ($favoriteGroup->getGroup() === $this) {
                $favoriteGroup->setGroup(null);
            }
        }

        return $this;
    }
}

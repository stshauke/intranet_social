<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: NotificationPreference::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $notificationPreferences;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: FavoriteGroup::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $favoriteGroups;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    public function __construct()
    {
        $this->notificationPreferences = new ArrayCollection();
        $this->favoriteGroups = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Nettoyage des données sensibles si besoin
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getUnreadNotifications(): array
    {
        return array_filter($this->notifications->toArray(), fn($n) => !$n->isRead());
    }

    public function getNotificationPreferences(): Collection
    {
        return $this->notificationPreferences;
    }

    public function addNotificationPreference(NotificationPreference $preference): self
    {
        if (!$this->notificationPreferences->contains($preference)) {
            $this->notificationPreferences[] = $preference;
            $preference->setUser($this);
        }

        return $this;
    }

    public function removeNotificationPreference(NotificationPreference $preference): self
    {
        if ($this->notificationPreferences->removeElement($preference)) {
            if ($preference->getUser() === $this) {
                $preference->setUser(null);
            }
        }

        return $this;
    }

    public function getNotificationPreferenceForType(string $type): ?NotificationPreference
    {
        foreach ($this->notificationPreferences as $pref) {
            if ($pref->getType() === $type) {
                return $pref;
            }
        }

        return null;
    }

    public function getFavoriteGroups(): Collection
    {
        return $this->favoriteGroups;
    }

    public function addFavoriteGroup(FavoriteGroup $favoriteGroup): self
    {
        if (!$this->favoriteGroups->contains($favoriteGroup)) {
            $this->favoriteGroups[] = $favoriteGroup;
            $favoriteGroup->setUser($this);
        }

        return $this;
    }

    public function removeFavoriteGroup(FavoriteGroup $favoriteGroup): self
    {
        if ($this->favoriteGroups->removeElement($favoriteGroup)) {
            if ($favoriteGroup->getUser() === $this) {
                $favoriteGroup->setUser(null);
            }
        }

        return $this;
    }
}

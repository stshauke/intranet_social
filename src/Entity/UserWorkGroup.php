<?php

namespace App\Entity;

use App\Repository\UserWorkGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserWorkGroupRepository::class)]
class UserWorkGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: WorkGroup::class, inversedBy: 'userLinks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WorkGroup $workGroup = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isAdmin = false;

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getWorkGroup(): ?WorkGroup { return $this->workGroup; }

    public function setWorkGroup(?WorkGroup $group): self
    {
        $this->workGroup = $group;
        return $this;
    }

    public function getIsAdmin(): bool { return $this->isAdmin; }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\FavoriteGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteGroupRepository::class)]
class FavoriteGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favoriteGroups')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: WorkGroup::class, inversedBy: 'favoriteGroups')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?WorkGroup $group = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getGroup(): ?WorkGroup
    {
        return $this->group;
    }

    public function setGroup(?WorkGroup $group): static
    {
        $this->group = $group;
        return $this;
    }
}

<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserWorkGroup;
use App\Entity\WorkGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserWorkGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWorkGroup::class);
    }

    /**
     * Vérifie si un utilisateur est membre d’un groupe.
     */
    public function isMember(User $user, WorkGroup $group): bool
    {
        return (bool) $this->createQueryBuilder('uwg')
            ->andWhere('uwg.user = :user')
            ->andWhere('uwg.workGroup = :group')
            ->setParameter('user', $user)
            ->setParameter('group', $group)
            ->getQuery()
            ->setMaxResults(1);
    }
}

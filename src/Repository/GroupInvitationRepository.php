<?php

namespace App\Repository;

use App\Entity\GroupInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupInvitation>
 */
class GroupInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupInvitation::class);
    }

    /**
     * Récupère toutes les invitations pour un utilisateur donné.
     */
    public function findByUser($user): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.invitedUser = :user')
            ->setParameter('user', $user)
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

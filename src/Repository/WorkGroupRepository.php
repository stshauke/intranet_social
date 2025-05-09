<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\WorkGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkGroup>
 */
class WorkGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkGroup::class);
    }

    /**
     * Retourne les groupes visibles pour un utilisateur.
     * - Publics pour tout le monde
     * - Privés ou secrets seulement pour les membres
     */
    public function findVisibleToUser(?User $user): array
    {
        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.userLinks', 'link')
            ->addSelect('link');

        if ($user) {
            $qb->where('g.type = :public')
                ->orWhere('link.user = :user')
                ->setParameter('public', 'public')
                ->setParameter('user', $user);
        } else {
            $qb->where('g.type = :public')
               ->setParameter('public', 'public');
        }

        return $qb->getQuery()->getResult();
    }
}

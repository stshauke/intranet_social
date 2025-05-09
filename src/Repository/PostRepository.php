<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Récupère les publications visibles par un utilisateur :
     * - Si le post est sans groupe, il est public
     * - Si le groupe est public, il est visible
     * - Si le groupe est privé/secret, seulement les membres/modérateurs peuvent le voir
     */
    public function findVisiblePosts(?User $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.workGroup', 'g')
            ->leftJoin('g.moderators', 'm')
            ->leftJoin('g.userLinks', 'link')
            ->addSelect('g');

        if ($user) {
            $qb->andWhere('g.id IS NULL OR g.type = :publicType')
                ->orWhere('link.user = :user')
                ->orWhere('m = :user')
                ->setParameter('publicType', 'public')
                ->setParameter('user', $user);
        } else {
            $qb->andWhere('g IS NULL OR g.type = :publicType')
               ->setParameter('publicType', 'public');
        }

        return $qb
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

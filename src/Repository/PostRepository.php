<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

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
     * Retourne un QueryBuilder filtrable par :
     * - type (publication, annonce, etc.)
     * - groupe
     * - auteur
     * - priorisation si l’utilisateur a des groupes favoris
     */
    public function createFilteredQueryBuilder(
        ?User $user,
        ?string $type = null,
        ?int $groupId = null,
        ?int $authorId = null,
        bool $prioritize = true
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.workGroup', 'g')
            ->leftJoin('g.moderators', 'm')
            ->leftJoin('g.userLinks', 'link')
            ->addSelect('g');

        // 🔐 Visibilité selon le groupe
        if ($user) {
            $qb->andWhere('g.id IS NULL OR g.type = :publicType OR link.user = :user OR m = :user')
               ->setParameter('publicType', 'public')
               ->setParameter('user', $user);
        } else {
            $qb->andWhere('g IS NULL OR g.type = :publicType')
               ->setParameter('publicType', 'public');
        }

        // 🔎 Filtres optionnels
        if ($type) {
            $qb->andWhere('p.type = :type')
               ->setParameter('type', $type);
        }

        if ($groupId) {
            $qb->andWhere('g.id = :groupId')
               ->setParameter('groupId', $groupId);
        }

        if ($authorId) {
            $qb->andWhere('p.author = :authorId')
               ->setParameter('authorId', $authorId);
        }

        // ⭐ Prioriser les publications de groupes favoris
        if ($user && $prioritize) {
            $favoriteGroupIds = array_map(
                fn($fg) => $fg->getGroup()->getId(),
                $user->getFavoriteGroups()->toArray()
            );

            if (!empty($favoriteGroupIds)) {
                $qb->addSelect('(CASE WHEN g.id IN (:favIds) THEN 0 ELSE 1 END) AS HIDDEN priorityGroup')
                   ->setParameter('favIds', $favoriteGroupIds)
                   ->orderBy('priorityGroup', 'ASC')
                   ->addOrderBy('p.createdAt', 'DESC');
            } else {
                $qb->orderBy('p.createdAt', 'DESC');
            }
        } else {
            $qb->orderBy('p.createdAt', 'DESC');
        }

        return $qb;
    }
}

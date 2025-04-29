<?php

namespace App\Repository;

use App\Entity\LikeComment;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LikeComment>
 */
class LikeCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LikeComment::class);
    }

    /**
     * Vérifie si un utilisateur a liké un commentaire.
     */
    public function isCommentLikedByUser(Comment $comment, User $user): bool
    {
        $like = $this->createQueryBuilder('lc')
            ->andWhere('lc.comment = :comment')
            ->andWhere('lc.user = :user')
            ->setParameter('comment', $comment)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $like !== null;
    }
}

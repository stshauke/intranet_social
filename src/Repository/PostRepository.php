<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findHomePosts(User $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.workGroup', 'g')
            ->leftJoin('g.userWorkGroupMembers', 'gm')
            ->where('gm.user = :user OR p.workGroup IS NULL')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findPublicPosts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.workGroup IS NULL')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

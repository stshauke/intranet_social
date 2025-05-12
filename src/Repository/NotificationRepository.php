<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findUnreadByUser(User $user, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.relatedPost', 'p')->addSelect('p')
            ->leftJoin('n.relatedComment', 'c')->addSelect('c')
            ->leftJoin('n.relatedMessage', 'm')->addSelect('m')
            ->where('n.user = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->orderBy('n.createdAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function countUnreadByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.user = :user')
            ->andWhere('n.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByUserAndType(User $user, string $type): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->andWhere('n.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function deleteOldReadNotifications(\DateTime $beforeDate): int
    {
        return $this->createQueryBuilder('n')
            ->delete()
            ->where('n.isRead = true')
            ->andWhere('n.createdAt < :beforeDate')
            ->setParameter('beforeDate', $beforeDate)
            ->getQuery()
            ->execute();
    }
}

<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Trouve les notifications non lues pour un utilisateur
     *
     * @param User $user L'utilisateur concerné
     * @param int|null $limit Limite optionnelle du nombre de résultats
     * @return Notification[] Tableau des notifications non lues
     */
    public function findUnreadByUser(User $user, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->andWhere('n.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->orderBy('n.createdAt', 'DESC');
        
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Compte le nombre de notifications non lues pour un utilisateur
     *
     * @param User $user L'utilisateur concerné
     * @return int Nombre de notifications non lues
     */
    public function countUnreadByUser(User $user): int
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.user = :user')
            ->andWhere('n.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les notifications d'un certain type pour un utilisateur
     *
     * @param User $user L'utilisateur concerné
     * @param string $type Le type de notification
     * @return Notification[] Tableau des notifications du type spécifié
     */
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

    /**
     * Supprime les anciennes notifications lues
     * 
     * @param \DateTime $beforeDate Supprimer les notifications lues avant cette date
     * @return int Nombre de notifications supprimées
     */
    public function deleteOldReadNotifications(\DateTime $beforeDate): int
    {
        return $this->createQueryBuilder('n')
            ->delete()
            ->where('n.isRead = :isRead')
            ->andWhere('n.createdAt < :beforeDate')
            ->setParameter('isRead', true)
            ->setParameter('beforeDate', $beforeDate)
            ->getQuery()
            ->execute();
    }
}
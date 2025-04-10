<?php

namespace App\Repository;

use App\Entity\GroupMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupMessage>
 *
 * @method GroupMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupMessage[]    findAll()
 * @method GroupMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMessage::class);
    }

    /**
     * Récupère les messages d'un groupe de travail
     *
     * @param int $workGroupId
     * @return GroupMessage[]
     */
    public function findByWorkGroup(int $workGroupId): array
    {
        return $this->createQueryBuilder('gm')
            ->andWhere('gm.workGroup = :groupId')
            ->setParameter('groupId', $workGroupId)
            ->orderBy('gm.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

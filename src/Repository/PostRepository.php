<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Requête personnalisée avec filtres dynamiques pour l'affichage des publications.
     */
    public function createFilteredQueryBuilder(
        User $user,
        ?string $type = null,
        ?int $groupId = null,
        ?int $authorId = null
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.workGroup', 'g')
            ->leftJoin('g.userLinks', 'link')
            ->addSelect('g')
            ->where('p.isDraft = false')
            ->andWhere('g.type = :public OR link.user = :user OR g.id IS NULL')
            ->setParameter('public', 'public')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC');

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

        return $qb;
    }

    /**
     * Récupère les brouillons d'un utilisateur.
     */
    public function findDraftsByUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.author = :user')
            ->andWhere('p.isDraft = true')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les posts contenant un tag spécifique via SQL natif.
     */
    public function findByTag(string $tag): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM post
            WHERE JSON_CONTAINS(tags, :tagJson) AND is_draft = false
            ORDER BY created_at DESC
        ';

        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'tagJson' => json_encode([$tag])
        ]);

        return $resultSet->fetchAllAssociative();
    }
}

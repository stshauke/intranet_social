<?php

// Déclaration du namespace du repository
namespace App\Repository;

// Importation des entités utilisées dans les requêtes
use App\Entity\Post;
use App\Entity\User;

// Importation des classes de base pour les repositories Doctrine
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

// Déclaration du repository pour l'entité Post
class PostRepository extends ServiceEntityRepository
{
    // Constructeur injectant le ManagerRegistry et liant le repository à l'entité Post
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // Récupère les publications à afficher sur la page d’accueil d’un utilisateur
    public function findHomePosts(User $user): array
    {
        $qb = $this->createQueryBuilder('p') // Création du QueryBuilder avec alias 'p' pour Post
            ->leftJoin('p.workGroup', 'g') // Jointure avec le groupe de travail
            ->leftJoin('g.userWorkGroupMembers', 'gm') // Jointure avec les membres du groupe
            ->where('gm.user = :user OR p.workGroup IS NULL') // Conditions : soit le user est membre du groupe, soit le post est public
            ->setParameter('user', $user) // Définition du paramètre : utilisateur connecté
            ->orderBy('p.createdAt', 'DESC'); // Tri par date de création décroissante

        return $qb->getQuery()->getResult(); // Exécution de la requête et retour des résultats
    }

    // Récupère toutes les publications publiques (non associées à un groupe)
    public function findPublicPosts(): array
    {
        return $this->createQueryBuilder('p') // Création du QueryBuilder
            ->where('p.workGroup IS NULL') // Filtre : seulement les posts publics
            ->orderBy('p.createdAt', 'DESC') // Tri par date décroissante
            ->getQuery()
            ->getResult(); // Exécution de la requête et retour des résultats
    }
}

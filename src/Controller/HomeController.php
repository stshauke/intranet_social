<?php

// Déclaration du namespace du contrôleur
namespace App\Controller;

// Importation des dépendances nécessaires
use App\Repository\PostRepository;                       // Pour accéder aux publications
use Knp\Component\Pager\PaginatorInterface;              // Pour la pagination
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Contrôleur de base Symfony
use Symfony\Component\HttpFoundation\Request;            // Pour gérer la requête HTTP
use Symfony\Component\HttpFoundation\Response;           // Pour gérer la réponse HTTP
use Symfony\Component\Routing\Attribute\Route;           // Pour définir les routes

// Définition de la classe contrôleur pour la page d'accueil
class HomeController extends AbstractController
{
    // Route pour la page d'accueil du site ('/')
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,                                 // Objet représentant la requête HTTP
        PostRepository $postRepository,                   // Repository pour accéder aux entités Post
        PaginatorInterface $paginator                     // Service de pagination KnpPaginator
    ): Response {
        // Création d'une requête Doctrine pour récupérer tous les posts triés par date décroissante
        $query = $postRepository->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')              // Classement des posts par date (du plus récent au plus ancien)
            ->getQuery();                                 // Génération de la requête finale

        // Application de la pagination sur les résultats
        $pagination = $paginator->paginate(
            $query,                                       // Requête à paginer
            $request->query->getInt('page', 1),           // Numéro de page actuel (défaut = 1)
            5                                             // Nombre d'éléments par page
        );

        // Affichage du template avec les données paginées
        return $this->render('home/index.html.twig', [
            'posts' => $pagination,                       // Envoi des posts paginés à la vue Twig
        ]);
    }
}

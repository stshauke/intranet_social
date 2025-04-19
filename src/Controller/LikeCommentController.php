<?php

// Déclaration du namespace du contrôleur
namespace App\Controller;

// Importation des entités, repositories, services et composants nécessaires
use App\Entity\Comment;
use App\Entity\LikeComment;
use App\Repository\LikeCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Définition du contrôleur pour la gestion des likes sur les commentaires
#[Route('/like-comment')]
class LikeCommentController extends AbstractController
{
    // Route pour liker ou unliker un commentaire via un POST classique
    #[Route('/{id}/like', name: 'app_comment_like', methods: ['POST'])]
    public function like(
        Comment $comment,                                  // Le commentaire concerné (résolu via l'ID)
        LikeCommentRepository $likeCommentRepository,      // Repository des likes sur commentaires
        EntityManagerInterface $entityManager              // Gestionnaire d'entité (BDD)
    ): Response {
        $user = $this->getUser();                          // Utilisateur actuellement connecté

        // Recherche si l'utilisateur a déjà liké ce commentaire
        $existingLike = $likeCommentRepository->findOneBy([
            'user' => $user,
            'comment' => $comment
        ]);

        // Si un like existe déjà, on le retire
        if ($existingLike) {
            $entityManager->remove($existingLike);
        } else {
            // Sinon, on crée un nouveau like
            $likeComment = new LikeComment();
            $likeComment->setUser($user);
            $likeComment->setComment($comment);
            $likeComment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($likeComment);
        }

        // On applique les changements en base
        $entityManager->flush();

        // Redirection vers la page du post contenant le commentaire
        return $this->redirectToRoute('app_post_show', ['id' => $comment->getPost()->getId()]);
    }

    // ✅ Route AJAX pour liker/unliker un commentaire sans rechargement de page
    #[Route('/{id}/like-ajax', name: 'app_comment_like_ajax', methods: ['POST'])]
    public function likeAjax(
        Comment $comment,                                  // Commentaire ciblé
        LikeCommentRepository $likeCommentRepository,      // Accès aux likes
        EntityManagerInterface $entityManager              // Accès base de données
    ): JsonResponse {
        $user = $this->getUser();                          // Utilisateur connecté

        // Si l'utilisateur n'est pas connecté, retour erreur 403
        if (!$user) {
            return new JsonResponse(['error' => 'Non authentifié'], 403);
        }

        // Vérifie si le like existe déjà pour ce commentaire et cet utilisateur
        $existingLike = $likeCommentRepository->findOneBy([
            'user' => $user,
            'comment' => $comment
        ]);

        // Si déjà liké : suppression du like
        if ($existingLike) {
            $entityManager->remove($existingLike);
            $liked = false;
        } else {
            // Sinon : création d’un nouveau like
            $likeComment = new LikeComment();
            $likeComment->setUser($user);
            $likeComment->setComment($comment);
            $likeComment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($likeComment);
            $liked = true;
        }

        // Exécution des modifications en base
        $entityManager->flush();

        // Récupère le nombre total de likes pour le commentaire
        $likeCount = $likeCommentRepository->count(['comment' => $comment]);

        // Réponse JSON contenant l'état du like et le nouveau compteur
        return new JsonResponse([
            'liked' => $liked,
            'likeCount' => $likeCount
        ]);
    }
}

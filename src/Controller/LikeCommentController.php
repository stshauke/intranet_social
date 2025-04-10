<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\LikeComment;
use App\Repository\LikeCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/like-comment')]
class LikeCommentController extends AbstractController
{
    #[Route('/{id}/like', name: 'app_comment_like', methods: ['POST'])]
    public function like(
        Comment $comment,
        LikeCommentRepository $likeCommentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        $existingLike = $likeCommentRepository->findOneBy([
            'user' => $user,
            'comment' => $comment
        ]);

        if ($existingLike) {
            $entityManager->remove($existingLike);
        } else {
            $likeComment = new LikeComment();
            $likeComment->setUser($user);
            $likeComment->setComment($comment);
            $likeComment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($likeComment);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_post_show', ['id' => $comment->getPost()->getId()]);
    }

    // ✅ La route AJAX pour les likes sans rechargement de page
    #[Route('/{id}/like-ajax', name: 'app_comment_like_ajax', methods: ['POST'])]
    public function likeAjax(
        Comment $comment,
        LikeCommentRepository $likeCommentRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Non authentifié'], 403);
        }

        $existingLike = $likeCommentRepository->findOneBy([
            'user' => $user,
            'comment' => $comment
        ]);

        if ($existingLike) {
            $entityManager->remove($existingLike);
            $liked = false;
        } else {
            $likeComment = new LikeComment();
            $likeComment->setUser($user);
            $likeComment->setComment($comment);
            $likeComment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($likeComment);
            $liked = true;
        }

        $entityManager->flush();

        $likeCount = $likeCommentRepository->count(['comment' => $comment]);

        return new JsonResponse([
            'liked' => $liked,
            'likeCount' => $likeCount
        ]);
    }
}

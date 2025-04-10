<?php
// src/Controller/CommentController.php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/new/{postId}', name: 'app_comment_new', methods: ['POST'])]
    public function new(
        int $postId,
        Request $request,
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository,
        NotificationService $notificationService
    ): Response {
        $post = $entityManager->getRepository(Post::class)->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('Post non trouvé');
        }

        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setPost($post);
        $comment->setCreatedAt(new \DateTimeImmutable()); // ✅ Important !

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            // ✅ Notification au propriétaire du post
            $notificationService->createNotification(
                'new_comment',
                'Un nouveau commentaire a été ajouté à votre publication.',
                $post->getUser(),
                $post,
                $comment
            );

            $this->addFlash('success', 'Commentaire ajouté avec succès.');
        }

        return $this->redirectToRoute('app_post_show', ['id' => $postId]);
    }

    #[Route('/delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Comment $comment,
        EntityManagerInterface $entityManager
    ): Response {
        $postId = $comment->getPost()->getId();

        if ($comment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire supprimé avec succès.');
        }

        return $this->redirectToRoute('app_post_show', ['id' => $postId]);
    }
}

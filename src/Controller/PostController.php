<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Attachment;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $user = $this->getUser();
        $posts = $postRepository->findVisiblePosts($user);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'post_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        NotificationService $notificationService
    ): Response {
        $post = new Post();
        $post->setAuthor($this->getUser());

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTimeImmutable());
            $em->persist($post);

            // 📎 Gérer les fichiers joints
            $files = $form->get('attachments')->getData();
            foreach ($files as $file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('attachments_directory'),
                        $newFilename
                    );

                    $attachment = new Attachment();
                    $attachment->setFilename($newFilename);
                    $attachment->setOriginalFilename($file->getClientOriginalName());
                    $attachment->setMimeType($file->getMimeType());
                    $attachment->setSize($file->getSize());
                    $attachment->setPost($post);

                    $em->persist($attachment);
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors du téléchargement d'un fichier joint.");
                }
            }

            $em->flush();

            // 🔔 Notifier les membres du groupe
            if ($post->getWorkGroup()) {
                $notificationService->notifyWorkGroupMembers($post);
            }

            $this->addFlash('success', 'Publication créée avec succès.');
            return $this->redirectToRoute('app_post_index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'])]
    public function show(
        Post $post,
        Request $request,
        EntityManagerInterface $em,
        CommentRepository $commentRepo,
        NotificationService $notificationService
    ): Response {
        $user = $this->getUser();
        $group = $post->getWorkGroup();

        // 🔐 Gérer les restrictions de groupe
        if ($group) {
            if ($group->getType() === 'private' && !$group->getUserLinks()->exists(fn ($i, $link) => $link->getUser() === $user)) {
                throw $this->createAccessDeniedException('Accès réservé aux membres du groupe.');
            }

            if ($group->getType() === 'secret' && !$group->getUserLinks()->exists(fn ($i, $link) => $link->getUser() === $user)) {
                throw $this->createNotFoundException('Publication introuvable.');
            }
        }

        // 💬 Traitement du formulaire de commentaire
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setAuthor($user);

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em->persist($comment);
            $em->flush();

            // 🔔 Notifier l’auteur du post s’il n’est pas l’auteur du commentaire
            if ($post->getAuthor() !== $user) {
                $notificationService->createNotification(
                    'new_comment',
                    sprintf('%s a commenté votre publication : "%s"', $user->getFullName(), $post->getTitle()),
                    $post->getAuthor(),
                    $post,
                    $comment
                );
            }

            $this->addFlash('success', 'Commentaire publié.');
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        $comments = $commentRepo->findBy(['post' => $post], ['createdAt' => 'ASC']);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $group = $post->getWorkGroup();
        $user = $this->getUser();

        if ($user !== $post->getAuthor() && (!$group || !$group->getModerators()->contains($user))) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Publication supprimée.');
        }

        return $group
            ? $this->redirectToRoute('workgroup_show', ['id' => $group->getId()])
            : $this->redirectToRoute('app_post_index');
    }

    #[Route('/{id}/like-ajax', name: 'app_post_like_ajax', methods: ['POST'])]
public function likeAjax(
    Post $post,
    LikeRepository $likeRepo,
    EntityManagerInterface $em,
    NotificationService $notificationService
): JsonResponse {
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['error' => 'Non autorisé'], 403);
    }

    $like = $likeRepo->findOneBy(['user' => $user, 'post' => $post]);

    if ($like) {
        $em->remove($like);
        $liked = false;
    } else {
        $like = new Like();
        $like->setUser($user);
        $like->setPost($post);
        $like->setCreatedAt(new \DateTimeImmutable());
        $em->persist($like);
        $liked = true;

        // 🔔 Notification : seulement si l'utilisateur n'est pas l'auteur
        if ($post->getAuthor() !== $user) {
            $notificationService->createNotification(
                'new_like',
                sprintf('%s a aimé votre publication : "%s"', $user->getFullName(), $post->getTitle()),
                $post->getAuthor(),
                $post
            );
        }
    }

    $em->flush();
    $likeCount = $likeRepo->count(['post' => $post]);

    return new JsonResponse([
        'liked' => $liked,
        'likeCount' => $likeCount,
    ]);
}
}

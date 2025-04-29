<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Like;
use App\Form\PostType;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\LikeCommentRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $postRepository->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('post/index.html.twig', [
            'posts' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, NotificationService $notificationService): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($post);
            $entityManager->flush();

            $notificationService->notifyWorkGroupMembers($post);

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
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository,
        LikeCommentRepository $likeCommentRepo,
        NotificationService $notificationService
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            if ($post->getUser() !== $this->getUser()) {
                $notificationService->createNotification(
                    'new_comment',
                    'Un utilisateur a commenté votre publication.',
                    $post->getUser(),
                    $post,
                    $comment
                );
            }

            $this->addFlash('success', 'Commentaire ajouté avec succès.');
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        $comments = $commentRepository->findBy(['post' => $post], ['createdAt' => 'ASC']);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $form->createView(),
            'comments' => $comments, // ✅ Ici j'envoie bien les comments
            'likeCommentRepo' => $likeCommentRepo,
        ]);
    }

    #[Route('/{id}/like-ajax', name: 'app_post_like_ajax', methods: ['POST'])]
    public function likeAjax(Post $post, LikeRepository $likeRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Vous devez être connecté pour aimer'], 403);
        }

        $like = $likeRepository->findOneBy(['user' => $user, 'post' => $post]);

        if ($like) {
            $entityManager->remove($like);
            $liked = false;
        } else {
            $like = new Like();
            $like->setUser($user);
            $like->setPost($post);
            $like->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($like);
            $liked = true;
        }

        $entityManager->flush();

        $likeCount = $likeRepository->count(['post' => $post]);

        return $this->json([
            'liked' => $liked,
            'likeCount' => $likeCount
        ]);
    }
}

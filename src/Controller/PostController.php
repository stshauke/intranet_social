<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Like;
use App\Entity\Comment;
use App\Entity\Attachment;
use App\Form\PostType;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\LikeRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\WorkGroupRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(
        Request $request,
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        WorkGroupRepository $groupRepo,
        UserRepository $userRepo
    ): Response {
        $user = $this->getUser();

        $type = $request->query->get('type');
        $groupId = $request->query->get('group');
        $authorId = $request->query->get('author');
        $tag = $request->query->get('tag');

        if ($tag) {
            $rawPosts = $postRepository->findByTag($tag);
            $posts = [];
            foreach ($rawPosts as $row) {
                $post = $postRepository->find($row['id']);
                if ($post) {
                    $posts[] = $post;
                }
            }

            $pagination = $paginator->paginate(
                $posts,
                $request->query->getInt('page', 1),
                6
            );
        } else {
            $queryBuilder = $postRepository->createFilteredQueryBuilder(
                $user,
                $type,
                $groupId ? (int)$groupId : null,
                $authorId ? (int)$authorId : null
            );

            $pagination = $paginator->paginate(
                $queryBuilder,
                $request->query->getInt('page', 1),
                6
            );
        }

        return $this->render('post/index.html.twig', [
            'pagination' => $pagination,
            'filters' => [
                'type' => $type,
                'group' => $groupId,
                'author' => $authorId,
                'tag' => $tag,
            ],
            'groups' => $groupRepo->findAll(),
            'authors' => $userRepo->findAll(),
        ]);
    }

    #[Route('/new', name: 'post_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        NotificationService $notificationService,
        UserRepository $userRepo
    ): Response {
        $post = new Post();
        $post->setAuthor($this->getUser());

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setTags($this->extractTags($post->getContent()));

            $mentions = $this->extractMentions($post->getContent());
            foreach ($mentions as $username) {
                $mentionedUser = $userRepo->findOneBy(['username' => $username]);
                if ($mentionedUser) {
                    $notificationService->createNotification(
                        'mention',
                        sprintf('%s vous a mentionné dans une publication : "%s"', $this->getUser()->getFullName(), $post->getTitle()),
                        $mentionedUser,
                        $post
                    );
                }
            }

            $em->persist($post);

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

            if ($post->getWorkGroup()) {
                $notificationService->notifyWorkGroupMembers($post);
            }

            $this->addFlash('success', $post->getIsDraft() ? 'Brouillon enregistré.' : 'Publication créée avec succès.');
            return $this->redirectToRoute('app_post_index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/drafts', name: 'post_drafts', methods: ['GET'])]
    public function listDrafts(PostRepository $postRepository): Response
    {
        $user = $this->getUser();
        $drafts = $postRepository->findDraftsByUser($user);

        return $this->render('post/drafts.html.twig', [
            'drafts' => $drafts,
        ]);
    }

    #[Route('/draft/{id}', name: 'post_draft_show', methods: ['GET'])]
    public function draftShow(int $id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Brouillon non trouvé.');
        }

        if (!$post->getIsDraft() || $post->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => [],
            'commentForm' => null,
        ]);
    }

    #[Route('/{id}/publish', name: 'post_publish_draft', methods: ['POST'])]
    public function publishDraft(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('publish' . $post->getId(), $request->request->get('_token'))) {
            if ($post->getIsDraft() && $post->getAuthor() === $this->getUser()) {
                $post->setIsDraft(false);
                $em->flush();

                $this->addFlash('success', 'Brouillon publié avec succès.');
            } else {
                $this->addFlash('danger', 'Action non autorisée.');
            }
        }

        return $this->redirectToRoute('post_drafts');
    }

    #[Route('/mention/search', name: 'mention_search', methods: ['GET'])]
    public function mentionSearch(Request $request, UserRepository $userRepo): JsonResponse
    {
        $query = $request->query->get('q', '');

        $users = $userRepo->createQueryBuilder('u')
            ->where('u.username LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $results = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName(),
            ];
        }, $users);

        return new JsonResponse($results);
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

        if ($group) {
            if ($group->getType() === 'private' && !$group->getUserLinks()->exists(fn ($i, $link) => $link->getUser() === $user)) {
                throw $this->createAccessDeniedException('Accès réservé aux membres du groupe.');
            }
            if ($group->getType() === 'secret' && !$group->getUserLinks()->exists(fn ($i, $link) => $link->getUser() === $user)) {
                throw $this->createNotFoundException('Publication introuvable.');
            }
        }

        $comment = new Comment();
        $comment->setPost($post);
        $comment->setAuthor($user);

        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $em->persist($comment);
            $em->flush();

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

    #[Route('/{id}/edit', name: 'post_edit')]
    public function edit(
        Request $request,
        Post $post,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->getUser() !== $post->getAuthor()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas l'auteur de cette publication.");
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setTags($this->extractTags($post->getContent()));
            $em->flush();

            $this->addFlash('success', 'Publication modifiée avec succès.');
            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    private function extractTags(string $content): array {
        preg_match_all('/#(\\w+)/u', $content, $matches);
        return array_unique($matches[1]);
    }

    private function extractMentions(string $content): array {
        preg_match_all('/@(\\w+)/u', $content, $matches);
        return array_unique($matches[1]);
    }
}

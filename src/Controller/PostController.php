<?php

// Déclaration du namespace du contrôleur
namespace App\Controller;

// Importation des entités utilisées
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Attachment;

// Importation des formulaires
use App\Form\PostType;
use App\Form\CommentType;

// Importation des repositories
use App\Repository\PostRepository;
use App\Repository\LikeRepository;
use App\Repository\CommentRepository;
use App\Repository\LikeCommentRepository;

// Importation du service de notification
use App\Service\NotificationService;

// Importation des composants Symfony et autres services
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

// Définition de la route principale pour ce contrôleur
#[Route('/post')]
class PostController extends AbstractController
{
    // Route pour afficher la liste paginée des publications
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Création de la requête Doctrine pour récupérer les publications triées par date
        $query = $postRepository->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();

        // Application de la pagination
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Page actuelle
            5 // Nombre d’éléments par page
        );

        // Rendu de la vue avec les publications paginées
        return $this->render('post/index.html.twig', [
            'posts' => $pagination,
        ]);
    }

    // Route pour créer une nouvelle publication
    #[Route('/new', name: 'app_post_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        NotificationService $notificationService
    ): Response {
        $post = new Post();

        // Création du formulaire
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdatedAt(new \DateTimeImmutable());

            // Gestion des fichiers attachés
            $attachmentFiles = $form->get('attachments')->getData() ?? [];
            foreach ($attachmentFiles as $attachmentFile) {
                $originalFilename = pathinfo($attachmentFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $attachmentFile->guessExtension();

                try {
                    // Déplacement du fichier dans le dossier configuré
                    $attachmentFile->move(
                        $this->getParameter('attachments_directory'),
                        $newFilename
                    );

                    // Création d'une entité Attachment
                    $attachment = new Attachment();
                    $attachment->setFilename($newFilename);
                    $attachment->setOriginalFilename($attachmentFile->getClientOriginalName());
                    $attachment->setMimeType($attachmentFile->getMimeType());
                    $attachment->setSize($attachmentFile->getSize());
                    $attachment->setPost($post);

                    $entityManager->persist($attachment);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement du fichier');
                }
            }

            // Sauvegarde du post
            $entityManager->persist($post);
            $entityManager->flush();

            // Notification aux membres du groupe
            $notificationService->notifyWorkGroupMembers($post);

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
        }

        // Affichage du formulaire de création
        return $this->render('post/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Route pour afficher une publication et gérer les commentaires
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

        // Si un commentaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            // Si ce n’est pas l’auteur, notifier celui-ci
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

        // Récupération des commentaires liés au post
        $comments = $commentRepository->findBy(['post' => $post], ['createdAt' => 'ASC']);

        // Affichage de la vue avec les commentaires et le formulaire
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $form->createView(),
            'comments' => $comments,
            'likeCommentRepo' => $likeCommentRepo,
        ]);
    }

    // Route AJAX pour aimer ou ne plus aimer un post
    #[Route('/{id}/like-ajax', name: 'app_post_like_ajax', methods: ['POST'])]
    public function likeAjax(Post $post, LikeRepository $likeRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Si l'utilisateur n'est pas connecté
        if (!$user) {
            return $this->json(['error' => 'Vous devez être connecté pour aimer'], 403);
        }

        // Recherche d'un like existant
        $like = $likeRepository->findOneBy(['user' => $user, 'post' => $post]);

        // Si déjà aimé, on le retire
        if ($like) {
            $entityManager->remove($like);
            $liked = false;
        } else {
            // Sinon, on enregistre le like
            $like = new Like();
            $like->setUser($user);
            $like->setPost($post);
            $like->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($like);
            $liked = true;
        }

        // Mise à jour de la base
        $entityManager->flush();

        // Calcul du nombre de likes
        $likeCount = $likeRepository->count(['post' => $post]);

        // Réponse JSON à renvoyer côté client
        return $this->json([
            'liked' => $liked,
            'likeCount' => $likeCount
        ]);
    }
}

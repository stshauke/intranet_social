<?php
// src/Controller/CommentController.php

namespace App\Controller;

// Importation des entités, formulaires, services, etc.
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

// Définition de la route de base pour ce contrôleur
#[Route('/comment')]
class CommentController extends AbstractController
{
    // Route pour ajouter un nouveau commentaire à un post spécifique
    #[Route('/new/{postId}', name: 'app_comment_new', methods: ['POST'])]
    public function new(
        int $postId,                                      // ID du post auquel le commentaire sera associé
        Request $request,                                 // Requête HTTP
        EntityManagerInterface $entityManager,            // Pour gérer la persistance des entités
        CommentRepository $commentRepository,             // Non utilisé ici, mais injecté
        NotificationService $notificationService          // Service pour envoyer des notifications
    ): Response {
        // Récupération du post à partir de son ID
        $post = $entityManager->getRepository(Post::class)->find($postId);

        // Si le post n'existe pas, on lève une exception 404
        if (!$post) {
            throw $this->createNotFoundException('Post non trouvé');
        }

        // Création d'une nouvelle instance de Comment
        $comment = new Comment();
        $comment->setUser($this->getUser());              // Attribution de l'utilisateur connecté
        $comment->setPost($post);                         // Association du post au commentaire
        $comment->setCreatedAt(new \DateTimeImmutable()); // ✅ Date de création du commentaire

        // Création et gestion du formulaire
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);           // Préparation à l'enregistrement
            $entityManager->flush();                     // Exécution de l'enregistrement

            // ✅ Envoi d'une notification au propriétaire du post
            $notificationService->createNotification(
                'new_comment',
                'Un nouveau commentaire a été ajouté à votre publication.',
                $post->getUser(),
                $post,
                $comment
            );

            // Message flash de succès
            $this->addFlash('success', 'Commentaire ajouté avec succès.');
        }

        // Redirection vers la page du post après traitement
        return $this->redirectToRoute('app_post_show', ['id' => $postId]);
    }

    // Route pour supprimer un commentaire existant
    #[Route('/delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(
        Request $request,                               // Requête HTTP
        Comment $comment,                               // Commentaire à supprimer (résolu par l'ID dans l'URL)
        EntityManagerInterface $entityManager           // Gestion de la base de données
    ): Response {
        // Récupération de l'ID du post lié au commentaire
        $postId = $comment->getPost()->getId();

        // Vérifie que l'utilisateur connecté est bien l'auteur du commentaire
        if ($comment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        // Vérifie le token CSRF pour la sécurité
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);           // Supprime le commentaire
            $entityManager->flush();                    // Applique les changements

            // Message flash de succès
            $this->addFlash('success', 'Commentaire supprimé avec succès.');
        }

        // Redirection vers la page du post après suppression
        return $this->redirectToRoute('app_post_show', ['id' => $postId]);
    }
}

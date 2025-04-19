<?php

// Déclaration du namespace du service
namespace App\Service;

// Importation des entités utilisées dans le service
use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\WorkGroup;

// Importation de l'EntityManager pour la gestion des entités
use Doctrine\ORM\EntityManagerInterface;

// Pour la date de création des notifications
use DateTimeImmutable;

// Déclaration du service NotificationService, responsable de la gestion des notifications
class NotificationService
{
    // Propriété contenant l'EntityManager
    private EntityManagerInterface $entityManager;

    // Injection de dépendance via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Création et enregistrement d'une notification en base de données
    public function createNotification(string $type, string $message, User $user, ?Post $post = null, ?Comment $comment = null): Notification
    {
        $notification = new Notification();
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setIsRead(false); // Par défaut la notification est non lue
        $notification->setCreatedAt(new DateTimeImmutable());

        // Ajoute un post associé à la notification si présent
        if ($post) {
            $notification->setRelatedPost($post);
        }

        // Ajoute un commentaire associé à la notification si présent
        if ($comment) {
            $notification->setRelatedComment($comment);
        }

        // Persist et flush pour enregistrer la notification
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    /**
     * ✅ Notifications pour les membres d’un groupe lorsqu’un post est publié
     */
    public function notifyWorkGroupMembers(Post $post): void
    {
        $workGroup = $post->getWorkGroup();

        if (!$workGroup) {
            return; // Si le post n'est associé à aucun groupe, on ne fait rien
        }

        foreach ($workGroup->getMembers() as $member) {
            // Ne pas notifier l’auteur du post
            if ($member === $post->getUser()) {
                continue;
            }

            // Message personnalisé pour la notification
            $message = sprintf('Nouvelle publication dans le groupe "%s" : %s', $workGroup->getName(), $post->getTitle());

            // Création de la notification pour chaque membre concerné
            $this->createNotification(
                'new_post',
                $message,
                $member,
                $post
            );
        }
    }

    /**
     * ✅ Notifications pour les membres d’un groupe lorsqu’un message est posté dans le forum
     */
    public function notifyGroupMembers(WorkGroup $workGroup, string $message): void
    {
        foreach ($workGroup->getMembers() as $member) {
            // Ne pas notifier le créateur du groupe
            if ($member === $workGroup->getCreator()) {
                continue;
            }

            // Création de la notification
            $this->createNotification(
                'group_message',
                $message,
                $member
            );
        }
    }

    // Récupère toutes les notifications non lues d’un utilisateur, triées par date décroissante
    public function getUnreadNotifications(User $user): array
    {
        return $this->entityManager->getRepository(Notification::class)->findBy(
            ['user' => $user, 'isRead' => false],
            ['createdAt' => 'DESC']
        );
    }

    // Marque une notification comme lue et enregistre en base
    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->entityManager->flush();
    }

    // Marque toutes les notifications d’un utilisateur comme lues
    public function markAllAsRead(User $user): void
    {
        $notifications = $this->entityManager->getRepository(Notification::class)->findBy(
            ['user' => $user, 'isRead' => false]
        );

        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }

        $this->entityManager->flush();
    }
}

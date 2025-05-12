<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\WorkGroup;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Crée une notification si l'utilisateur a activé ce type.
     */
    public function createNotification(
        string $type,
        string $message,
        User $user, // ⚠️ Le destinataire de la notification
        ?Post $post = null,
        ?Comment $comment = null,
        ?Message $relatedMessage = null
    ): ?Notification {
        $preference = $user->getNotificationPreferenceForType($type);
        if ($preference && !$preference->isEnabled()) {
            return null;
        }

        $notification = new Notification();
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setUser($user);
        $notification->setIsRead(false);
        $notification->setCreatedAt(new \DateTimeImmutable());

        if ($post) {
            $notification->setRelatedPost($post);
        }

        if ($comment) {
            $notification->setRelatedComment($comment);
        }

        if ($relatedMessage) {
            $notification->setRelatedMessage($relatedMessage);
        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    public function notifyWorkGroupMembers(Post $post): void
    {
        $workGroup = $post->getWorkGroup();
        if (!$workGroup) {
            return;
        }

        foreach ($workGroup->getMembers() as $member) {
            if ($member === $post->getUser()) {
                continue;
            }

            $message = sprintf('Nouvelle publication dans le groupe "%s" : %s', $workGroup->getName(), $post->getTitle());

            $this->createNotification(
                'new_post',
                $message,
                $member,
                $post
            );
        }
    }

    public function notifyGroupMembers(WorkGroup $workGroup, string $message): void
    {
        foreach ($workGroup->getMembers() as $member) {
            $this->createNotification(
                'group_message',
                $message,
                $member
            );
        }
    }

    public function getUnreadNotifications(User $user): array
    {
        return $this->entityManager->getRepository(Notification::class)->findBy(
            ['user' => $user, 'isRead' => false],
            ['createdAt' => 'DESC']
        );
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->entityManager->flush();
    }

    public function markAllAsRead(User $user): void
    {
        $notifications = $this->entityManager->getRepository(Notification::class)->findBy([
            'user' => $user,
            'isRead' => false
        ]);

        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }

        $this->entityManager->flush();
    }
}

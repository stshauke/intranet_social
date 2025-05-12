<?php

namespace App\EventListener;

use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\Post;
use App\Service\NotificationService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\Events;

class NotificationSubscriber implements EventSubscriberInterface
{
    private NotificationService $notificationService;
    private Security $security;

    public function __construct(NotificationService $notificationService, Security $security)
    {
        $this->notificationService = $notificationService;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(object $entity, LifecycleEventArgs $args): void
    {
        if ($entity instanceof Comment) {
            $post = $entity->getPost();
            $author = $post->getAuthor();

            // Ne pas notifier soi-même
            if ($author !== $entity->getAuthor()) {
                $message = sprintf('%s a commenté votre publication : "%s"', $entity->getAuthor()->getFullName(), $post->getTitle());

                $this->notificationService->createNotification(
                    'new_comment',
                    $message,
                    $author,
                    $post,
                    $entity
                );
            }

        } elseif ($entity instanceof Like) {
            $post = $entity->getPost();
            $author = $post->getAuthor();
            $liker = $entity->getUser();

            if ($author !== $liker) {
                $message = sprintf('%s aime votre publication : "%s"', $liker->getFullName(), $post->getTitle());

                $this->notificationService->createNotification(
                    'like',
                    $message,
                    $author,
                    $post
                );
            }

        } elseif ($entity instanceof Post) {
            // Notification aux membres du groupe si publication dans un groupe
            if ($entity->getWorkGroup()) {
                $this->notificationService->notifyWorkGroupMembers($entity);
            }
        }
    }
}

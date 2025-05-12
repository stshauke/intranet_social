<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notification')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'app_notification')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/mark-all-read', name: 'app_notification_mark_all_read', methods: ['POST'])]
    public function markAllRead(EntityManagerInterface $entityManager, NotificationRepository $repo): Response
    {
        $notifications = $repo->findBy([
            'user' => $this->getUser(),
            'isRead' => false
        ]);

        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_notification');
    }

    #[Route('/{id}/mark-read', name: 'app_notification_mark_read', methods: ['POST'])]
    public function markRead(Notification $notification, EntityManagerInterface $entityManager): Response
    {
        if ($notification->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if (!$notification->isRead()) {
            $notification->setIsRead(true);
            $entityManager->flush();
        }

        return new Response(null, 204);
    }

    #[Route('/ajax/unread', name: 'notifications_ajax_unread', methods: ['GET'])]
    public function unreadNotifications(NotificationRepository $repo): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['count' => 0, 'notifications' => []]);
        }

        $notifications = $repo->findUnreadByUser($user, 10);

        $data = [];

        foreach ($notifications as $notif) {
            $url = '#';

            if ($notif->getRelatedPost()) {
                $url = $this->generateUrl('app_post_show', ['id' => $notif->getRelatedPost()->getId()]);
            } elseif ($notif->getRelatedComment()) {
                $url = $this->generateUrl('app_post_show', [
                    'id' => $notif->getRelatedComment()->getPost()->getId()
                ]) . '#comment-' . $notif->getRelatedComment()->getId();
            } elseif ($notif->getRelatedMessage()) {
                $url = $this->generateUrl('app_message_read', [
                    'id' => $notif->getRelatedMessage()->getId()
                ]);
            }

            $data[] = [
                'id' => $notif->getId(),
                'message' => $notif->getMessage(),
                'url' => $url,
            ];
        }

        return new JsonResponse([
            'count' => count($data),
            'notifications' => $data,
        ]);
    }
}

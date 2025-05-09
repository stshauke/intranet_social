<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function markAllRead(
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $notifications = $notificationRepository->findBy([
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

        return new Response(null, 204); // 204 = succès silencieux
    }
}

<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestNotificationController extends AbstractController
{
    #[Route('/test/message-notifications', name: 'test_message_notifications')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        // 🔎 DEBUG : Affiche l'utilisateur connecté
        dump($this->getUser()->getId());      // ID de l'utilisateur
        dd($this->getUser()->getEmail());     // Email de l'utilisateur

        $user = $this->getUser();

        if (!$user) {
            return new Response('Utilisateur non connecté.', 403);
        }

        $notifications = $notificationRepository->createQueryBuilder('n')
            ->where('n.user = :user')
            ->andWhere('n.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', 'message')
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $html = "<h2>🔔 Test des notifications de type \"message\"</h2>";

        if (empty($notifications)) {
            $html .= '<p>⚠️ Aucune notification de message trouvée.</p>';
        } else {
            $html .= '<ul>';
            foreach ($notifications as $notif) {
                $html .= '<li><strong>' . htmlspecialchars($notif->getMessage()) . '</strong><br>';
                $html .= 'Date : ' . $notif->getCreatedAt()->format('d/m/Y H:i') . '</li>';
            }
            $html .= '</ul>';
        }

        return new Response($html);
    }
}

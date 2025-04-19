<?php

// Déclaration du namespace
namespace App\Controller;

// Importation des dépendances nécessaires
use App\Repository\NotificationRepository;                 // Repository pour accéder aux notifications
use Doctrine\ORM\EntityManagerInterface;                   // Interface pour les opérations avec la base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Classe de base pour les contrôleurs
use Symfony\Component\HttpFoundation\Response;             // Objet de réponse HTTP
use Symfony\Component\Routing\Annotation\Route;            // Annotation pour définir les routes

// Définition de la route de base pour toutes les actions de ce contrôleur
#[Route('/notification')]
class NotificationController extends AbstractController
{
    // Route pour afficher la liste des notifications de l'utilisateur connecté
    #[Route('/', name: 'app_notification_index')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        // Récupère les notifications de l'utilisateur actuel, triées par date décroissante
        $notifications = $notificationRepository->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        // Rend la vue Twig avec les notifications à afficher
        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    // Route pour marquer toutes les notifications comme lues (via POST uniquement)
    #[Route('/mark-all-read', name: 'app_notification_mark_all_read', methods: ['POST'])]
    public function markAllRead(
        NotificationRepository $notificationRepository,     // Repository pour les notifications
        EntityManagerInterface $entityManager               // Accès à la base de données
    ): Response {
        // Récupère toutes les notifications non lues de l'utilisateur connecté
        $notifications = $notificationRepository->findBy([
            'user' => $this->getUser(),
            'isRead' => false
        ]);

        // Parcourt chaque notification et la marque comme lue
        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
        }

        // Applique les changements dans la base de données
        $entityManager->flush();

        // Message flash de confirmation
        $this->addFlash('success', 'Toutes vos notifications ont été marquées comme lues.');

        // Redirection vers la page des notifications
        return $this->redirectToRoute('app_notification_index');
    }
}

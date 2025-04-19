<?php

// Déclaration du namespace du contrôleur
namespace App\Controller;

// Importation des entités nécessaires
use App\Entity\UserWorkGroup;
use App\Entity\WorkGroup;

// Importation des composants Doctrine et Symfony
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Définition de la route principale pour toutes les actions liées à l'adhésion à un groupe
#[Route('/user/workgroup')]
class UserWorkGroupController extends AbstractController
{
    // Route permettant à un utilisateur de rejoindre un groupe de travail
    #[Route('/join/{id}', name: 'app_user_workgroup_join', methods: ['GET'])]
    public function join(
        WorkGroup $workGroup,                          // Groupe de travail ciblé (résolu via l'ID)
        EntityManagerInterface $entityManager          // Interface pour la gestion des entités
    ): Response {
        $user = $this->getUser();                      // Récupération de l'utilisateur connecté

        // Vérifie si l'utilisateur est déjà membre du groupe
        foreach ($workGroup->getUserWorkGroupMembers() as $member) {
            if ($member->getUser() === $user) {
                // Affiche un message d'information et redirige vers la page du groupe
                $this->addFlash('info', 'Vous êtes déjà membre de ce groupe.');
                return $this->redirectToRoute('app_work_group_show', ['id' => $workGroup->getId()]);
            }
        }

        // Création de l'association entre l'utilisateur et le groupe
        $userWorkGroup = new UserWorkGroup();
        $userWorkGroup->setUser($user);                           // L'utilisateur actuel
        $userWorkGroup->setWorkGroup($workGroup);                 // Le groupe ciblé
        $userWorkGroup->setIsAdmin(false);                        // Rôle par défaut : non-admin
        $userWorkGroup->setJoinedAt(new \DateTimeImmutable());    // ✅ Date d'adhésion (nécessaire pour éviter erreur SQL)

        // Persistance de l'entité en base de données
        $entityManager->persist($userWorkGroup);
        $entityManager->flush();

        // Message de confirmation
        $this->addFlash('success', 'Vous avez rejoint le groupe avec succès.');

        // Redirection vers la page du groupe
        return $this->redirectToRoute('app_work_group_show', ['id' => $workGroup->getId()]);
    }
}

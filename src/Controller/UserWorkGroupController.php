<?php

namespace App\Controller;

use App\Entity\UserWorkGroup;
use App\Entity\WorkGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/workgroup')]
class UserWorkGroupController extends AbstractController
{
    #[Route('/join/{id}', name: 'app_user_workgroup_join', methods: ['GET'])]
    public function join(
        WorkGroup $workGroup,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        // Vérifier si l'utilisateur est déjà membre
        foreach ($workGroup->getUserWorkGroupMembers() as $member) {
            if ($member->getUser() === $user) {
                $this->addFlash('info', 'Vous êtes déjà membre de ce groupe.');
                return $this->redirectToRoute('app_work_group_show', ['id' => $workGroup->getId()]);
            }
        }

        $userWorkGroup = new UserWorkGroup();
        $userWorkGroup->setUser($user);
        $userWorkGroup->setWorkGroup($workGroup);
        $userWorkGroup->setIsAdmin(false);
        $userWorkGroup->setJoinedAt(new \DateTimeImmutable()); // ✅ Important pour éviter l'erreur SQL !

        $entityManager->persist($userWorkGroup);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez rejoint le groupe avec succès.');

        return $this->redirectToRoute('app_work_group_show', ['id' => $workGroup->getId()]);
    }
}

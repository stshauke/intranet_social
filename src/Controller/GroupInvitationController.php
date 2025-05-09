<?php

namespace App\Controller;

use App\Entity\GroupInvitation;
use App\Entity\UserWorkGroup;
use App\Entity\WorkGroup;
use App\Form\GroupInvitationType;
use App\Repository\GroupInvitationRepository;
use App\Repository\UserRepository;
use App\Repository\UserWorkGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/group-invitation')]
class GroupInvitationController extends AbstractController
{
    #[Route('/{id}/invite', name: 'group_invitation_invite', methods: ['GET', 'POST'])]
    public function invite(
        Request $request,
        WorkGroup $workGroup,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();

        // Vérifier si l'utilisateur est membre ou créateur ou modérateur
        $isMember = $em->getRepository(UserWorkGroup::class)->findOneBy([
            'user' => $user,
            'workGroup' => $workGroup
        ]);

        if (
            !$isMember &&
            $workGroup->getCreator() !== $user &&
            !$workGroup->getModerators()->contains($user)
        ) {
            throw $this->createAccessDeniedException('Seuls les membres ou le créateur peuvent inviter.');
        }

        $invitation = new GroupInvitation();
        $invitation->setGroup($workGroup);
        $invitation->setInvitedBy($user);

        $form = $this->createForm(GroupInvitationType::class, $invitation, [
            'current_user' => $user,
            'work_group' => $workGroup,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($invitation);
            $em->flush();

            $this->addFlash('success', 'Invitation envoyée avec succès.');

            return $this->redirectToRoute('workgroup_show', ['id' => $workGroup->getId()]);
        }

        return $this->render('group_invitation/invite.html.twig', [
            'form' => $form->createView(),
            'workGroup' => $workGroup,
        ]);
    }

    #[Route('/{id}/accept', name: 'group_invitation_accept', methods: ['POST'])]
    public function accept(
        GroupInvitation $invitation,
        EntityManagerInterface $em,
        UserWorkGroupRepository $userWorkGroupRepo
    ): Response {
        $user = $this->getUser();

        if ($invitation->getInvitedUser() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à accepter cette invitation.");
        }

        $link = new UserWorkGroup();
        $link->setUser($user);
        $link->setWorkGroup($invitation->getGroup());
        $link->setIsAdmin(false);

        $em->persist($link);
        $em->remove($invitation);
        $em->flush();

        $this->addFlash('success', 'Vous avez rejoint le groupe.');

        return $this->redirectToRoute('workgroup_show', [
            'id' => $invitation->getGroup()->getId()
        ]);
    }

    #[Route('/{id}/decline', name: 'group_invitation_decline', methods: ['POST'])]
    public function decline(
        GroupInvitation $invitation,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if ($invitation->getInvitedUser() !== $user) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à refuser cette invitation.");
        }

        $em->remove($invitation);
        $em->flush();

        $this->addFlash('info', 'Invitation refusée.');

        return $this->redirectToRoute('app_dashboard_invitations');
    }

    #[Route('/{id}/delete', name: 'group_invitation_delete', methods: ['POST'])]
    public function delete(
        GroupInvitation $invitation,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('delete' . $invitation->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        if (
            $invitation->getInvitedBy() !== $user &&
            $invitation->getInvitedUser() !== $user
        ) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à supprimer cette invitation.");
        }

        $em->remove($invitation);
        $em->flush();

        $this->addFlash('success', 'Invitation supprimée.');

        return $this->redirectToRoute('app_dashboard_invitations');
    }
}

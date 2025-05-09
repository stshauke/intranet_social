<?php

namespace App\Controller;

use App\Repository\GroupInvitationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/invitations', name: 'app_dashboard_invitations')]
    public function invitations(GroupInvitationRepository $repo): Response
    {
        $user = $this->getUser();

        $received = $repo->findBy(['invitedUser' => $user]);
        $sent = $repo->findBy(['invitedBy' => $user]);

        return $this->render('dashboard/invitations.html.twig', [
            'invitations_received' => $received,
            'invitations_sent' => $sent,
        ]);
    }
}

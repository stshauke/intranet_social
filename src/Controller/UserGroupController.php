<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserGroupController extends AbstractController
{
    #[Route('/user/group', name: 'app_user_group')]
    public function index(): Response
    {
        return $this->render('user_group/index.html.twig', [
            'controller_name' => 'UserGroupController',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/favorites')]
class FavoriteController extends AbstractController
{
    #[Route('/', name: 'favorites_index')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Récupère les groupes favoris de l'utilisateur
        $favorites = $user->getFavoriteGroups()->map(fn($fav) => $fav->getGroup());

        return $this->render('favorite/index.html.twig', [
            'groups' => $favorites,
        ]);
    }
}

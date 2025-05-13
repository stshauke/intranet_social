<?php

namespace App\Controller;

use App\Entity\FavoriteGroup;
use App\Entity\WorkGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/favorite-group')]
class FavoriteGroupController extends AbstractController
{
    #[Route('/toggle/{id}', name: 'toggle_favorite_group')]
    public function toggleFavorite(WorkGroup $group, EntityManagerInterface $em): RedirectResponse
    {
        $user = $this->getUser();

        // Vérifie si le groupe est déjà en favori
        $existing = null;
        foreach ($user->getFavoriteGroups() as $fav) {
            if ($fav->getGroup()->getId() === $group->getId()) {
                $existing = $fav;
                break;
            }
        }

        if ($existing) {
            $user->removeFavoriteGroup($existing);
            $em->remove($existing);
            $this->addFlash('info', 'Groupe retiré de vos favoris.');
        } else {
            $favorite = new FavoriteGroup();
            $favorite->setUser($user);
            $favorite->setGroup($group);
            $em->persist($favorite);
            $user->addFavoriteGroup($favorite);
            $this->addFlash('success', 'Groupe ajouté à vos favoris.');
        }

        $em->flush();

        return $this->redirectToRoute('workgroup_show', ['id' => $group->getId()]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevController extends AbstractController
{
    #[Route('/dev/generate-posts', name: 'dev_generate_posts')]
    public function generatePosts(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();
        $types = ['publication', 'annonce'];

        if (count($users) < 1) {
            return new Response('Aucun utilisateur trouvé. Ajoute d’abord des utilisateurs.');
        }

        foreach ($users as $user) {
            foreach ($types as $type) {
                $post = new Post();
                $post->setTitle(ucfirst($type) . " test utilisateur #" . $user->getId());
                $post->setContent("Contenu généré automatiquement pour le type : $type.");
                $post->setType($type);
                $post->setAuthor($user);
                $em->persist($post);
            }
        }

        $em->flush();

        return new Response(count($users) . " utilisateurs x 2 types de publication ajoutés = OK");
    }
}

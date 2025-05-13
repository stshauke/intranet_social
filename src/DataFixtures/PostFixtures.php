<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur de test
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setFullName('Utilisateur Test');
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $manager->persist($user);

        // Créer des posts avec différents types
        $types = ['publication', 'annonce'];

        foreach ($types as $type) {
            for ($i = 1; $i <= 3; $i++) {
                $post = new Post();
                $post->setTitle(ucfirst($type) . " d'exemple $i");
                $post->setContent("Ceci est une $type de démonstration numéro $i.");
                $post->setAuthor($user);
                $post->setType($type);
                $manager->persist($post);
            }
        }

        $manager->flush();
    }
}

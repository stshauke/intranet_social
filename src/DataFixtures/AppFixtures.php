<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\WorkGroup;
use App\Entity\UserWorkGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $users = [];

        // ✅ Crée 5 utilisateurs
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email);
            $user->setFullName($faker->name);
            $user->setRoles(['ROLE_USER']);
            $user->setBio($faker->sentence);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTime());

            // Mot de passe simple pour tous les utilisateurs : "password"
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            // Pas de photo de profil pour certains utilisateurs pour tester 👌
            if ($i % 2 === 0) {
                $user->setProfileImage(null);
            } else {
                $user->setProfileImage('default.jpg'); // Image par défaut
            }

            $manager->persist($user);
            $users[] = $user;
        }

        // ✅ Crée 2 groupes
        $groups = [];
        for ($i = 1; $i <= 2; $i++) {
            $group = new WorkGroup();
            $group->setName("Groupe " . $i);
            $group->setDescription($faker->sentence);
            $group->setIsPrivate(false);
            $group->setCreatedAt(new \DateTimeImmutable());
            $group->setUpdatedAt(new \DateTime());

            $manager->persist($group);
            $groups[] = $group;

            // Ajoute des membres au groupe
            foreach ($users as $user) {
                $userWorkGroup = new UserWorkGroup();
                $userWorkGroup->setUser($user);
                $userWorkGroup->setWorkGroup($group);
                $userWorkGroup->setIsAdmin($faker->boolean);
                $userWorkGroup->setJoinedAt(new \DateTimeImmutable());

                $manager->persist($userWorkGroup);
            }
        }

        // ✅ Crée 10 publications
        for ($i = 1; $i <= 10; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence(6, true));
            $post->setContent($faker->paragraph(3));
            $post->setCreatedAt(new \DateTimeImmutable());
            $post->setUpdatedAt(new \DateTimeImmutable());
            $post->setUser($faker->randomElement($users));
            $post->setWorkGroup($faker->randomElement($groups));

            $manager->persist($post);
        }

        $manager->flush();
    }
}

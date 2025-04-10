<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ✅ Création de l’administrateur
        $admin = new User();
        $admin->setEmail('admin@intranet.com');
        $admin->setFullName('Administrateur');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setCreatedAt(new \DateTimeImmutable());
        $admin->setUpdatedAt(new \DateTime());

        // Mot de passe encodé
        $hashedAdminPassword = $this->passwordHasher->hashPassword($admin, 'adminpass');
        $admin->setPassword($hashedAdminPassword);

        // Image de profil par défaut ou spécifique pour l'admin
        $admin->setProfileImage('default.png');

        $manager->persist($admin);

        // ✅ Création des utilisateurs normaux
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setFullName("Utilisateur $i");
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTime());

            // Mot de passe encodé
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            // Image de profil par défaut
            $user->setProfileImage('default.png');

            $manager->persist($user);
        }

        $manager->flush();
    }
}

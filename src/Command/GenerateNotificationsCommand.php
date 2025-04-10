<?php

namespace App\Command;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate-notifications',
    description: 'Génère des notifications de test pour les utilisateurs existants',
)]
class GenerateNotificationsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        $users = $userRepository->findAll();

        if (empty($users)) {
            $output->writeln('<error>Aucun utilisateur trouvé. Créez d’abord un utilisateur.</error>');
            return Command::FAILURE;
        }

        foreach ($users as $user) {
            for ($i = 1; $i <= 5; $i++) {
                $notification = new Notification();
                $notification->setUser($user);
                $notification->setType('test');
                $notification->setMessage("Notification de test n°{$i} pour {$user->getFullName()}");
                $notification->setIsRead(false);
                $notification->setCreatedAt(new \DateTimeImmutable());

                $this->entityManager->persist($notification);
            }
        }

        $this->entityManager->flush();

        $output->writeln('<info>✅ 5 notifications ont été créées pour chaque utilisateur.</info>');

        return Command::SUCCESS;
    }
}

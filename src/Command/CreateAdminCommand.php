<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates an admin user',
)]
class CreateAdminCommand extends Command
{
    private $entityManager;
    private $passwordHasher;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Check if admin already exists
        $existing = $this->userRepository->findOneBy(['email' => 'admin@hms.com']);
        if ($existing) {
            $io->warning('Admin user already exists!');
            $io->table(
                ['Email', 'Password'],
                [['admin@hms.com', 'password']]
            );
            return Command::SUCCESS;
        }

        // Create Admin User
        $adminUser = new User();
        $adminUser->setEmail('admin@hms.com');
        $adminUser->setFullName('System Administrator');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'password'));
        
        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();

        $io->success('Admin user created successfully!');
        $io->table(
            ['Email', 'Password', 'Role'],
            [['admin@hms.com', 'password', 'ROLE_ADMIN']]
        );

        return Command::SUCCESS;
    }
}

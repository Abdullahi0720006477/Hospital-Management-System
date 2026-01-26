<?php

use App\Entity\User;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__.'/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    $kernel->boot();

    $container = $kernel->getContainer();
    $em = $container->get('doctrine')->getManager();
    $hasher = $container->get('security.user_password_hasher');
    $userRepo = $em->getRepository(User::class);

    $email = 'admin@hms.com';
    $user = $userRepo->findOneBy(['email' => $email]);

    if (!$user) {
        $user = new User();
        $user->setEmail($email);
        $user->setFullName('System Admin');
    }

    $user->setRoles(['ROLE_ADMIN']);
    $password = $hasher->hashPassword($user, 'password');
    $user->setPassword($password);

    $em->persist($user);
    $em->flush();

    echo "User $email upserted successfully.\n";
};

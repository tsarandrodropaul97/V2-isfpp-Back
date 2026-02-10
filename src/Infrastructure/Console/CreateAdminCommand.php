<?php

namespace App\Infrastructure\Console;

use App\Infrastructure\Doctrine\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create:admin',
    description: 'Creates a new admin user.',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Email', null, function ($email) {
            $errors = $this->validator->validate($email, [
                new Assert\NotBlank(),
                new Assert\Email(),
            ]);

            if (count($errors) > 0) {
                throw new \InvalidArgumentException($errors[0]->getMessage());
            }

            $existing = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existing) {
                throw new \InvalidArgumentException('Email already exists');
            }

            return $email;
        });

        $password = $io->askHidden('Password', function ($password) {
            $errors = $this->validator->validate($password, [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 8]),
                new Assert\Regex([
                    'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                    'message' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.'
                ]),
            ]);

            if (count($errors) > 0) {
                throw new \InvalidArgumentException($errors[0]->getMessage());
            }

            return $password;
        });

        $confirmPassword = $io->askHidden('Confirm password');

        if ($password !== $confirmPassword) {
            $io->error('Passwords do not match');
            return Command::FAILURE;
        }

        $firstName = $io->ask('First Name (optional)');
        $lastName = $io->ask('Last Name (optional)');

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setIsActive(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Admin user %s created successfully.', $email));

        return Command::SUCCESS;
    }
}

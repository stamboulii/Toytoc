<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;

#[AsCommand(
    name       : 'app:user:create',
    description: 'This command create a user in database',
)]
class UserCreateCommand extends Command
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('firstName', InputArgument::REQUIRED, 'User firstname')
            ->addArgument('lastName', InputArgument::REQUIRED, 'User lastname');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = (new User())
            ->setEmail($input->getArgument('email'))
            ->setFirstName($input->getArgument('firstName'))
            ->setLastName($input->getArgument('lastName'))
            ->setRoles(['ROLE_ADMIN']);

        $user->setPassword($this->passwordHasher->hashPassword($user, $input->getArgument('password')));

        try {
            $this->repository->save($user, true);

            $io->success(sprintf('The user %s has been successfully created !', $user->getFullName()));
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }
}

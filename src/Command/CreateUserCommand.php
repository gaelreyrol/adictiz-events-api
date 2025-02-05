<?php

namespace Adictiz\Command;

use Adictiz\Entity\User;
use Adictiz\Entity\ValueObject\UserEmail;
use Adictiz\Entity\ValueObject\UserId;
use Adictiz\Exception\AbstractUserException;
use Adictiz\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'adictiz:user:create',
    description: 'Create a new user',
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The user email')
            ->addArgument('password', InputArgument::REQUIRED, 'The user password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        try {
            $this->userService->create(new User(new UserId(), new UserEmail($email), $password));
        } catch (AbstractUserException $exception) {
            $io->error(array_filter([
                'An error occurred while creating the user',
                $exception->getMessage(),
                $exception->getPrevious()?->getMessage(),
            ]));

            return Command::FAILURE;
        }

        $io->success('The user has been created successfully!');

        return Command::SUCCESS;
    }
}

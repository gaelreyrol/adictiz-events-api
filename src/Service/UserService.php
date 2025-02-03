<?php

namespace Adictiz\Service;

use Adictiz\Entity\User;
use Adictiz\Entity\ValueObject\UserId;
use Adictiz\Exception\UserCreationFailureException;
use Adictiz\Exception\UserNotFoundException;
use Adictiz\Repository\UserRepository;
use Psr\Log\LoggerInterface;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function get(UserId $id): User
    {
        $user = $this->userRepository->find($id);
        if (false === $user instanceof User) {
            throw new UserNotFoundException($id);
        }

        return $user;
    }

    public function create(User $user): User
    {
        try {
            $this->userRepository->save($user);
        } catch (\Exception $exception) {
            $this->logger->error('Failed to create user', [
                'user_id' => (string) $user->getId(),
                'user_email' => (string) $user->getEmail(),
                'exception' => $exception->getMessage(),
            ]);
            throw new UserCreationFailureException($exception);
        }

        return $user;
    }
}

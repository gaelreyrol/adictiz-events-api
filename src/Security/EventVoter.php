<?php

namespace Adictiz\Security;

use Adictiz\Entity\Event;
use Adictiz\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Event>
 */
class EventVoter extends Voter
{
    public const string VIEW = 'view';
    public const string UPDATE = 'update';
    public const string DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::UPDATE, self::DELETE])) {
            return false;
        }

        if (false === $subject instanceof Event) {
            return false;
        }

        return true;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return in_array($attribute, [self::VIEW, self::UPDATE, self::DELETE], true);
    }

    public function supportsType(string $subjectType): bool
    {
        return Event::class === $subjectType;
    }

    /**
     * @param Event $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (false === $user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::VIEW, self::UPDATE, self::DELETE => $this->isOwner($user, $subject),
            default => throw new \InvalidArgumentException('Event voter does not support this attribute.'),
        };
    }

    private function isOwner(User $user, Event $event): bool
    {
        return $user === $event->getOwner();
    }
}

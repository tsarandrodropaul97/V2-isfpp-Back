<?php

namespace App\Infrastructure\Security;

use App\Infrastructure\Doctrine\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTimeImmutable;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('Your account is inactive.');
        }

        if ($user->getLockedUntil() && $user->getLockedUntil() > new DateTimeImmutable()) {
            $diff = $user->getLockedUntil()->getTimestamp() - time();
            $minutes = ceil($diff / 60);
            throw new CustomUserMessageAuthenticationException(sprintf('Account locked. Please try again in %d minutes.', $minutes));
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // No checks needed after authentication
    }
}

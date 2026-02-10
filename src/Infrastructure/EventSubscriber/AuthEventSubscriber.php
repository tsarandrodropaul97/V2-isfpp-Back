<?php

namespace App\Infrastructure\EventSubscriber;

use App\Infrastructure\Doctrine\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent as SecurityAuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use DateTimeImmutable;

class AuthEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LoginFailureEvent::class => 'onLoginFailure',
            Events::AUTHENTICATION_SUCCESS => 'onJwtAuthenticationSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $user->setLastLoginAt(new DateTimeImmutable());
        $user->setLoginAttempts(0);
        $user->setLockedUntil(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $passport = $event->getPassport();
        if (!$passport) {
            return;
        }

        $user = $passport->getUser();
        if (!$user instanceof User) {
            return;
        }

        $user->incrementLoginAttempts();
        if ($user->getLoginAttempts() >= 5) {
            $user->setLockedUntil((new DateTimeImmutable())->modify('+30 minutes'));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function onJwtAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $data = $event->getData();
        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
        ];

        $event->setData($data);
    }
}

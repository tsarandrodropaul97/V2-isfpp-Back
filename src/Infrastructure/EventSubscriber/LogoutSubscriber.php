<?php

namespace App\Infrastructure\EventSubscriber;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(private RefreshTokenManagerInterface $refreshTokenManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $request = $event->getRequest();
        $content = json_decode($request->getContent(), true);
        $refreshTokenString = $content['refreshToken'] ?? null;

        if ($refreshTokenString) {
            $refreshToken = $this->refreshTokenManager->get($refreshTokenString);
            if ($refreshToken) {
                $this->refreshTokenManager->delete($refreshToken);
            }
        }

        $response = new JsonResponse(['message' => 'Logged out successfully']);
        $event->setResponse($response);
    }
}

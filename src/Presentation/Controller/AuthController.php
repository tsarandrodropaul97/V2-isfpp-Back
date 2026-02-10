<?php

namespace App\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Attributes as OA;

class AuthController extends AbstractController
{
    #[Route('/api/auth/me', name: 'api_me', methods: ['GET'])]
    #[OA\Get(
        summary: "Récupérer l'utilisateur connecté",
        tags: ["Authentification"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Détails de l'utilisateur",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "user", type: "object", properties: [
                            new OA\Property(property: "id", type: "string", format: "uuid"),
                            new OA\Property(property: "email", type: "string", format: "email"),
                            new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string")),
                            new OA\Property(property: "firstName", type: "string"),
                            new OA\Property(property: "lastName", type: "string")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Non authentifié")
        ]
    )]
    public function me(?UserInterface $user): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        return new JsonResponse([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ]
        ]);
    }

    #[Route('/api/auth/login', name: 'api_login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/login',
        summary: "Authentification utilisateur",
        tags: ["Authentification"],
        requestBody: new OA\RequestBody(
            description: "Identifiants de connexion",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token JWT généré avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", description: "Le token JWT d'authentification")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Identifiants invalides")
        ]
    )]
    public function login(): void
    {
        // This method is handled by the security system (json_login)
        // It is defined here only for Swagger documentation
    }
}

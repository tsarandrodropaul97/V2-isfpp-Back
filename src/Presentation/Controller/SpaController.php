<?php

namespace App\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SpaController extends AbstractController
{
    #[Route('/{reactRouting}', name: 'app_spa', requirements: ['reactRouting' => '^(?!api).*'], defaults: ['reactRouting' => null], priority: -1)]
    public function index(): Response
    {
        $indexPath = __DIR__ . '/../../../public/index.html';

        if (!file_exists($indexPath)) {
            return new Response(
                '<html><body><h1>React App Not Found</h1><p>Please build your frontend and copy the files to <code>backend/public/</code>.</p></body></html>',
                Response::HTTP_NOT_FOUND
            );
        }

        return new Response(file_get_contents($indexPath));
    }
}

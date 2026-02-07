<?php

namespace App\Presentation\Controller\Api;

use App\Application\DTO\CreateNewsDTO;
use App\Application\DTO\UpdateNewsDTO;
use App\Application\UseCase\CreateNewsUseCase;
use App\Application\UseCase\DeleteNewsUseCase;
use App\Application\UseCase\GetAllNewsUseCase;
use App\Application\UseCase\GetNewsByCategoryUseCase;
use App\Application\UseCase\GetNewsUseCase;
use App\Application\UseCase\GetPublishedNewsUseCase;
use App\Application\UseCase\UpdateNewsUseCase;
use App\Presentation\Request\CreateNewsRequest;
use App\Presentation\Request\UpdateNewsRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use App\Infrastructure\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Nelmio\ApiDocBundle\Attribute\Model;
use App\Application\DTO\NewsDTO;

#[Route('/api/news')]
#[OA\Tag(name: "News")]
class NewsController extends AbstractController
{
    public function __construct(
        private FileUploader $fileUploader
    ) {
    }
    /**
     * Liste toutes les actualités (BackOffice).
     */
    #[Route('', methods: ['GET'])]
    #[OA\Response(response: 200, description: "Liste complète des actualités", content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: new Model(type: NewsDTO::class))))]
    public function list(GetAllNewsUseCase $useCase): JsonResponse
    {
        $news = $useCase->execute();
        return $this->json($news);
    }

    /**
     * Liste les actualités publiées (FrontOffice).
     */
    #[Route('/published', methods: ['GET'])]
    #[OA\Parameter(name: "limit", in: "query", description: "Nombre maximum d'actualités", schema: new OA\Schema(type: "integer"))]
    public function published(Request $request, GetPublishedNewsUseCase $useCase): JsonResponse
    {
        $limit = $request->query->get('limit');
        $news = $useCase->execute($limit ? (int)$limit : null);
        return $this->json($news);
    }

    /**
     * Voir une actualité.
     */
    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, GetNewsUseCase $useCase): JsonResponse
    {
        try {
            $news = $useCase->execute($id);
            return $this->json($news);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Voir par catégorie.
     */
    #[Route('/category/{category}', methods: ['GET'])]
    public function byCategory(string $category, Request $request, GetNewsByCategoryUseCase $useCase): JsonResponse
    {
        $publishedOnly = $request->query->getBoolean('published_only', true);
        try {
            $news = $useCase->execute($category, $publishedOnly);
            return $this->json($news);
        } catch (\ValueError $e) {
             return $this->json(['error' => "Catégorie invalide"], 400);
        }
    }

    /**
     * Créer une actualité.
     */
    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                required: ["title", "category", "content", "imageFile"],
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "category", type: "string"),
                    new OA\Property(property: "content", type: "string"),
                    new OA\Property(property: "imageFile", type: "string", format: "binary"),
                    new OA\Property(property: "publish", type: "boolean")
                ]
            )
        )
    )]
    public function create(
        Request $request,
        CreateNewsUseCase $useCase
    ): JsonResponse {
        try {
            $title = $request->request->get('title');
            $category = $request->request->get('category');
            $content = $request->request->get('content');
            $publish = $request->request->getBoolean('publish', false);
            
            /** @var UploadedFile|null $imageFile */
            $imageFile = $request->files->get('imageFile');
            $imageUrl = null;

            if ($imageFile) {
                $imageUrl = $this->fileUploader->upload($imageFile);
            }

            $dto = new CreateNewsDTO(
                (string)$title,
                (string)$category,
                (string)$content,
                $imageUrl,
                $publish
            );

            $news = $useCase->execute($dto);
            return $this->json($news, 201);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'trace' => $this->getParameter('kernel.debug') ? $e->getTraceAsString() : null
            ], 400);
        }
    }

    /**
     * Mettre à jour une actualité.
     */
    #[Route('/{id}', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[OA\RequestBody(
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "category", type: "string"),
                    new OA\Property(property: "content", type: "string"),
                    new OA\Property(property: "imageFile", type: "string", format: "binary"),
                    new OA\Property(property: "status", type: "string")
                ]
            )
        )
    )]
    public function update(
        int $id,
        Request $request,
        UpdateNewsUseCase $useCase
    ): JsonResponse {
        try {
            $title = $request->request->get('title');
            $category = $request->request->get('category');
            $content = $request->request->get('content');
            $status = $request->request->get('status');
            
            /** @var UploadedFile|null $imageFile */
            $imageFile = $request->files->get('imageFile');
            $imageUrl = null;

            if ($imageFile) {
                $imageUrl = $this->fileUploader->upload($imageFile);
            }

            $dto = new UpdateNewsDTO(
                $id,
                $title ? (string)$title : null,
                $category ? (string)$category : null,
                $content ? (string)$content : null,
                $imageUrl,
                $status ? (string)$status : null
            );

            $news = $useCase->execute($dto);
            return $this->json($news);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'trace' => $this->getParameter('kernel.debug') ? $e->getTraceAsString() : null
            ], 400);
        }
    }

    /**
     * Supprimer une actualité.
     */
    #[Route('/{id}', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id, DeleteNewsUseCase $useCase): JsonResponse
    {
        try {
            $useCase->execute($id);
            return $this->json(['message' => 'Actualité supprimée']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}

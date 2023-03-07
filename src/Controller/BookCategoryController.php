<?php

namespace App\Controller;

use App\Models\BookCategoryListResponse;
use App\Service\BookCategoryService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BookCategoryController extends AbstractController
{
    public function __construct(private BookCategoryService $bookCategoryService)
    {
    }

    // Дословно как должен вернуть модель при 200 ответе
    /**
     * @OA\Response(
     *     response=200,
     *     description="returns books categories",
     *
     *     @Model(type=BookCategoryListResponse::class))
     */
    #[Route(path: 'api/v1/book/categories', name: 'app_book_categories', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->bookCategoryService->getCategories()
        );
    }
}

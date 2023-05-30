<?php

namespace App\Controller;

use App\Exceptions\BookCategoryNotFoundException;
use App\Models\BookListResponse;
use App\Service\BookService;
use http\Exception\RuntimeException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Models\ErrorResponse;
use App\Models\BookDetails;


class BookController extends AbstractController
{
    public function __construct(private BookService $bookService)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="returns publisher books by category",
     *
     *     @Model(type=BookListResponse::class))
     * @OA\Response(
     *     response=404,
     *     description="Book Category not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: 'api/v1/category/{id}/books', methods:['GET'])]
    public function booksByCategory(int $id): Response
    {
        return $this->json($this->bookService->getBookByCategory($id));
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="returns published books details information",
     *
     *     @Model(type=BookDetails::class))
     * @OA\Response(
     *     response=404,
     *     description="Book not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: 'api/v1/book/{id}', methods:['GET'])]
    public function booksById(int $id): Response
    {
        return $this->json($this->bookService->getBookById($id));
    }
}

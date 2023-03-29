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


class BookController extends AbstractController
{
    public function __construct(private BookService $bookService)
    {
    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="returns books by category",
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
        //throw new \RuntimeException();
        return $this->json($this->bookService->getBookByCategory($id));
    }
}

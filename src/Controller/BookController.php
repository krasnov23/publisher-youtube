<?php

namespace App\Controller;

use App\Exceptions\BookCategoryNotFoundException;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Models\BookListResponse;

class BookController extends AbstractController
{

    public function __construct(private BookService $bookService)
    {

    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="returns books by category",
     *     @Model(type=BookListResponse::class))
     */
    #[Route(path: 'api/v1/category/{id}/books')]
    public function booksByCategory(int $id): Response
    {
        try {
            return $this->json($this->bookService->getBookByCategory($id));
        } catch (BookCategoryNotFoundException $e){

            throw new HttpException($e->getCode(),$e->getMessage());
        }
    }

}
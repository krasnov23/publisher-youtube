<?php

namespace App\Controller;



use App\Service\BookCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BookCategoryController extends AbstractController
{
    public function __construct(private BookCategoryService $bookCategoryService)
    {
    }

    #[Route('/book/category', name: 'app_book_category')]
    public function index(): JsonResponse
    {
        return $this->json([
            $this->bookCategoryService->getCategories()
        ]);
    }
}

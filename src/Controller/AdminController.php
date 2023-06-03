<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Models\Author\BookCategoryUpdateRequest;
use App\Models\IdResponse;
use App\Service\BookCategoryService;
use App\Service\RoleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;
use App\Models\ErrorResponse;


class AdminController extends AbstractController
{
    public function __construct(private RoleService $roleService,
                                private BookCategoryService $bookCategoryService)
    {
    }


    // Хэштег тэг штука для группировки роутов внутри слаггера.
    /**
     * @OA\Tag(name="Admin API")
     * @OA\Response(
     *     response=200,
     *     description="Grants ROLE_AUTHOR to a user",
     * )
     *  @OA\Response(
     *     response=404,
     *     description="User not found",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route('/api/v1/admin/grantAuthor/{userId}', name: 'app_admin',methods: ['POST'])]
    // Данный роут требует права админа
    public function grantAuthor(int $userId): Response
    {
        $this->roleService->grandAuthor($userId);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Admin API")
     * @OA\Response(
     *     response=200,
     *     description="Category deleted",
     * )
     *  @OA\Response(
     *     response=404,
     *     description="Book category not found",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Book category still contains books",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    #[Route(path: '/api/v1/admin/bookCategory/{categoryId}/delete', methods: ['DELETE'])]
    public function deleteCategory(int $categoryId): Response
    {
        $this->bookCategoryService->deleteCategory($categoryId);

        return $this->json(null);
    }

    /**
     * @OA\Tag(name="Admin API")
     * @OA\Response(
     *     response=200,
     *     description="Create a new category",
     *     @Model(type=IdResponse::class)
     * )
     *  @OA\Response(
     *     response=409,
     *     description="Book category slug already exists",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=BookCategoryUpdateRequest::class))
     */
    #[Route(path: '/api/v1/admin/bookCategory', methods: ['POST'])]
    public function createCategory(#[RequestBody] BookCategoryUpdateRequest $updateRequest): Response
    {
        return $this->json($this->bookCategoryService->createCategory($updateRequest));
    }

    /**
     * @OA\Tag(name="Admin API")
     * @OA\Response(
     *     response=200,
     *     description="Update a book category",
     *     @Model(type=IdResponse::class)
     * )
     *  @OA\Response(
     *     response=409,
     *     description="Book category slug already exists",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=BookCategoryUpdateRequest::class))
     */
    #[Route(path: '/api/v1/admin/bookCategory/{categoryId}/edit', methods: ['POST'])]
    public function editCategory(int $categoryId,#[RequestBody] BookCategoryUpdateRequest $updateRequest): Response
    {
        $this->bookCategoryService->upDateCategory($categoryId,$updateRequest);

        return $this->json(null);
    }












}

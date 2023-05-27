<?php

namespace App\Controller;

use App\Service\RoleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;
use App\Models\ErrorResponse;


class AdminController extends AbstractController
{
    public function __construct(private RoleService $roleService)
    {
    }


    /**
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

    


}

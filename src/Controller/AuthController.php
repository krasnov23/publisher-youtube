<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Models\SignUpRequest;
use App\Service\SignUpService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Models\IdResponse;
use App\Models\ErrorResponse;


class AuthController extends AbstractController
{
    public function __construct(private SignUpService $signUpService)
    {

    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Signs up a user",
     *     @OA\JsonContent(
     *          @OA\Property(property="token",type="string"),
     *          @OA\Property(property="refresh_token",type="string"),
     *      )
     * )
     * @OA\Response(
     *     response=409,
     *     description="User Already Exists",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Validation Failed",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=SignUpRequest::class))
     */
    #[Route('/api/v1/auth/signUp', name: 'app_sign_up',methods: ['POST'])]
    public function signUp(#[RequestBody] SignUpRequest $signUpRequest): Response
    {
        // В данном случае возвращаем просто сервис так как возврат сервиса итак несет в себе json объект
        return $this->signUpService->signUp($signUpRequest);
    }




}

<?php

namespace App\Controller;

use App\Service\RecommendationService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Models\RecommendedBookListResponse;
use OpenApi\Annotations as OA;

class RecommendationController extends AbstractController
{


    public function __construct(private RecommendationService $recommendationService)
    {
    }

    /**
     * @OA\Response(
     *      response = 200,
     *      description = "Returns Recommendations of Books",
     *      @Model(type=RecommendedBookListResponse::class)
     * )
     */
    #[Route('/api/v1/book/{id}/recommendations', name: 'app_recommendation',methods: ['GET'])]
    public function recommendationsByBookId(int $id): Response
    {
        return $this->json($this->recommendationService->getRecommendationsByBookId($id));
    }


}

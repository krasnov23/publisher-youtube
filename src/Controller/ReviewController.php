<?php

namespace App\Controller;

use App\Service\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use App\Models\ReviewPage;

class ReviewController extends AbstractController
{
    public function __construct(private ReviewService $reviewService)
    {

    }

    // В строке ниже задокументировали что принимаем page как параметр
    /**
     * @OA\Parameter(name="page",in="query",description="Page number",@OA\Schema(type="integer"))
     * @OA\Response(
     *     response=200,
     *     description="returns page of reviews for the given book",
     *     @Model(type=ReviewPage::class))
     */
    #[Route('/api/v1/book/{id}/reviews', name: 'app_review',methods: ['GET'])]
    public function index(int $id,Request $request): Response
    {
        return $this->json($this->reviewService->getReviewPageByBookId($id,$request->query->get('page',1)));
    }
}

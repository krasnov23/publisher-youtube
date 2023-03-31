<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Service\SubscriberService;
use App\Models\SubscriberRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Repository\SubscriberRepository;
use App\Models\ErrorResponse;


class SubscribeController extends AbstractController
{
    public function __construct(private SubscriberService $subscriberService)
    {

    }

    // Модель при двухсотом ответе не указываем, потому что ничего не возвращает
    /**
     * @OA\Response(
     *     response=200,
     *     description="Subscribe email to newsletter mailing list",
     * )
     * @OA\Response(
     *     response=400,
     *     description="We give not valid data",
     *     @Model(type=ErrorResponse::class)
     * )
     * @OA\RequestBody(@Model(type=SubscriberRequest::class))
     */
    #[Route(path: '/api/v1/subscribe', name: 'app_subscribe', methods: ['POST'])]
    // с помощью атрибута App/Attribute/RequestBody
    public function action(#[RequestBody] SubscriberRequest $subscriberRequest): Response
    {
        // Передаем СабскрайбСервису наш объект в котором будет емейл и разрешение на рассылку емейлов в виде (true/false)
        $this->subscriberService->subscribe($subscriberRequest);

        return $this->json(null);

    }
}

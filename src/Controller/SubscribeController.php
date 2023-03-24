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


class SubscribeController extends AbstractController
{
    public function __construct(private SubscriberService $subscriberService)
    {

    }

    /**
     * @OA\Response(
     *     response=200,
     *     description="Subscribe email to newsletter mailing list",
     *
     *     @Model(type=SubscriberRepository::class))
     */
    #[Route(path: '/api/v1/subscribe', name: 'app_subscribe', methods: ['POST'])]
    // с помощью атрибута src/Attribute/RequestBody
    public function action(#[RequestBody] SubscriberRequest $subscriberRequest): Response
    {
        // Передаем СабскрайбСервису наш объект в котором будет емейл и разрешение на рассылку емейлов в виде (true/false)
        $this->subscriberService->subscribe($subscriberRequest);

        return $this->json(null);

    }
}

<?php

namespace App\Listener;

use App\Exceptions\ValidationException;
use App\Models\ErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ValidationExceptionListener
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function __invoke(ExceptionEvent $exceptionEvent)
    {
        // Получаем наше исключение
        $throwable = $exceptionEvent->getThrowable();

        // Является ли исключение ValidationException и если оно им не является это не наш клиент мы его пропускаем
        if (!($throwable instanceof ValidationException)) {
            return;
        }

        // Сереализируем наши данные об ошибке в Json
        $data = $this->serializer->serialize(new ErrorResponse($throwable->getMessage(),
            ['violations' => $throwable->getViolation()]),
            JsonEncoder::FORMAT);


        // Возврат ответ в JsonFormatе , json = true означает что на вход мы даем уже сериализированный json
        $exceptionEvent->setResponse(new JsonResponse($data, Response::HTTP_BAD_REQUEST ,[],true));
    }
}

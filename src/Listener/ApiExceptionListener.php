<?php

namespace App\Listener;

use App\Models\ErrorDebugDetails;
use App\Models\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListener
{

    public function __construct(private ExceptionMappingResolver $resolver,
                                private LoggerInterface $logger,
                                private SerializerInterface $serializer,
                                // Переменная isDebug была определенна в services.yaml, при построении сервиса
                                // Оно добавится автоматически из параметров.
                                private bool  $isDebug)
    {
    }


    // Класс ExceptionEvent перехватывает исключение
    public function __invoke(ExceptionEvent $event): void
    {
        // Получает исключение из ExceptionEvent
        $throwable = $event->getThrowable();

        // Пробуем найти исключение среди наших исключений
        $mapping = $this->resolver->resolve(get_class($throwable));

        // Если исключение не найдено среди наших исключений, то задаем код ответа 500.
        if (null === $mapping)
        {
            $mapping = ExceptionMapping::fromCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Если код 500+ или свойство логирования нашего маппинга тру, то возвращает логи об ошибке
        if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable())
        {
            // Возвращает trace нашей ошибки в консоль если установлен monolog-bundle
            $this->logger->error($throwable->getMessage(),[
                'trace' => $throwable->getTraceAsString(),
                // возвращает ничего если предыдущего исключения не было и сообщение о предыдущем исключении если оно было
                // это нужно на всякий случай может там будет храниться исключение
                'previous' => null !== $throwable->getPrevious() ? $throwable->getPrevious()->getMessage() : ''
            ]);

        }


        // Если сообщение из исключения скрыто, то выводим стандартное сообщение, в остальных случаях указанный нами текст сообщения
        // который находится непосредственно в классе исключения
        $message = $mapping->isHidden() ? Response::$statusTexts[$mapping->getCode()] : $throwable->getMessage();

        // Если Debug true то возвращает трейс ошибки
        $details = $this->isDebug ? new ErrorDebugDetails($throwable->getTraceAsString()): null;

        // Сериализирует наш ответ в Json Формат, ErrorResponse возвращает нам ответ json на нашем экране (т.е пользователю)
        $data = $this->serializer->serialize(new ErrorResponse($message,$details), JsonEncoder::FORMAT);

        // Сформируем наш ответ. пустой массив это хедеры мы тут их не передаем, json = true говорит о том что данные уже в
        // Json не нужно их кодировать
        $response = new JsonResponse($data, $mapping->getCode(),[],true);

        // Назначаем этот ответ клиенту.
        $event->setResponse($response);

    }

}
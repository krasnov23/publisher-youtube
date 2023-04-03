<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class AbstractTestCase extends TestCase
{
    protected function setEntityId(object $entity, int $value, $idField = 'id')
    {
        $class = new \ReflectionClass($entity);

        // Из нашей рефлексии класса нам нужно получить поле айди сделать его открытым, установить ему значение и вернуть обратно
        // в закрытое состояние
        $property = $class->getProperty($idField);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }

    protected function assertResponse(int $expectedStatusCode,string $expectedBody, Response $actualResponse): void
    {
        $this->assertEquals($expectedStatusCode,$actualResponse->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class,$actualResponse);
        $this->assertJsonStringEqualsJsonString($expectedBody,$actualResponse->getContent());

    }

    // Аргумент Throwable передается потому что мы бы хотели получать разные исключения
    protected function createExceptionEvent(\Throwable $e): ExceptionEvent
    {

        return new ExceptionEvent(

        // kernel - ядро
            $this->createTestKernel(),
            // Request пустой потому что он нам впринципе не нужен
            new Request(),
            // MainRequest основной запрос который приходит от клиента, в процессе его обработки могут быть запущенны
            // еще запросы СубРеквесты нас же интересует только один запрос (в данном случае)
            HttpKernelInterface::MAIN_REQUEST,
            $e
        );


    }

    // просто наследуем HttpKernelInterface и он возвращает простой ответ
    protected function createTestKernel(): HttpKernelInterface
    {
        // На любой запрос он возвращает ответ с содержимым тест
        return new class() implements HttpKernelInterface
        {

            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                return new Response('testing');
            }
        };
    }


}

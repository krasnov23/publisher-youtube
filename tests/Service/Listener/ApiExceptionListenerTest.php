<?php

namespace App\Tests\Service\Listener;

use App\Listener\ApiExceptionListener;
use App\Models\ErrorDebugDetails;
use App\Models\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use InvalidArgumentException;


class ApiExceptionListenerTest extends AbstractTestCase
{

    private ExceptionMappingResolver $resolver;

    private LoggerInterface $logger;

    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        // Создали мок объект класса ExceptionMappingResolver
        $this->resolver = $this->createMock(ExceptionMappingResolver::class);

        // Мы не задаем Логгеру никакую бизнес логику, он ничего не возвращает и не влияет на работу функционала
        // Обычно его просто мокают и его не настраивают
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->serializer = $this->createMock(SerializerInterface::class);


    }

    // Тест на ошибку со скрытым сообщением
    public function testNon500MappingWithHiddenMessage(): void
    {
        // Возвращает массив в виде класса с кодом ответа, хайденом, логаблом
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);

        // Получает стандартный текст от ошибки
        $response = Response::$statusTexts[$mapping->getCode()];

        // Ответ в формате джесон
        $responseBody = json_encode(['error' => $response]);

        // Резолвер ожидает что будет вызван метод резолв с аргументом InvalidArgumentException::class который вернет объект
        // ExceptionMapping c указанными ключами и значениями
        $this->resolver->expects($this->once())->method('resolve')
            ->with(InvalidArgumentException::class)->willReturn($mapping);

        // Сериалайзер ожидает метода сериалайз, в который будут переданы класс ошибки с сообщением об ошибке в формате джсон
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($response),JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Создаем ошибку
        $event = $this->createExceptionEvent(new InvalidArgumentException('test'));

        $this->runListener($event);

        // Получает ответ
        $response = $event->getResponse();

        // Код ответа один и тот же
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        // Является объектом ответа
        $this->assertInstanceOf(JsonResponse::class,$response);

        // Сравниваем
        $this->assertJsonStringEqualsJsonString($responseBody,$response->getContent());
    }

    // Тест на ошибку с нескрытым сообщением
    public function testNon500MappingWithPublicMessage()
    {
        // Возвращает массив в виде класса с кодом ответа, хайденом, логаблом
        // В данном случае в отличии от теста выше мы задаем hidden = false для того чтобы сообщение не было скрыто
        // В методе getfromcode, в тесте выше не указываются второй и третий параметры потому что они уже стоят
        // по умолчанию в этом методе
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND,false,false);

        // По сравнению с первым тестом заменили сообщение
        $response = 'test';

        // Ответ в формате джесон
        $responseBody = json_encode(['error' => $response]);

        // Резолвер ожидает что будет вызван метод резолв с аргументом InvalidArgumentException::class который вернет объект
        // ExceptionMapping c указанными ключами и значениями
        $this->resolver->expects($this->once())->method('resolve')
            ->with(InvalidArgumentException::class)->willReturn($mapping);

        // Сериалайзер ожидает метода сериалайз, в который будут переданы класс ошибки с сообщением об ошибке в формате джсон
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($response),JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Перехватывает ошибку
        $event = $this->createExceptionEvent(new InvalidArgumentException('test'));


        $this->runListener($event);

        // Получает ответ
        $response = $event->getResponse();


        // Код ответа один и тот же
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        // Является объектом ответа
        $this->assertInstanceOf(JsonResponse::class,$response);

        // Сравниваем
        $this->assertJsonStringEqualsJsonString($responseBody,$response->getContent());


    }

    // Данный тест на проверку того что логгер реально вызывается
    public function testNon500LoggableMappingTriggerLogger(): void
    {
        // loggable - true потому что логируем данный Exception
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND,false,true);

        // По сравнению с первым тестом заменили сообщение
        $response = 'test';

        // Ответ в формате джесон
        $responseBody = json_encode(['error' => $response]);

        // Резолвер ожидает что будет вызван метод резолв с аргументом InvalidArgumentException::class который вернет объект
        // ExceptionMapping c указанными ключами и значениями
        $this->resolver->expects($this->once())->method('resolve')
            ->with(InvalidArgumentException::class)->willReturn($mapping);

        // Сериалайзер ожидает метода сериалайз, в который будут переданы класс ошибки с сообщением об ошибке в формате джсон
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($response),JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Этого достаточно чтобы понимать что логер действительно вызывается
        // Нам важно проверить что логгер работает так как он попадает под условие и это условие
        // (if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable())) -
        // нам сверх критически важно
        $this->logger->expects($this->once())
            ->method('error');

        // Перехватывает ошибку
        $event = $this->createExceptionEvent(new InvalidArgumentException('test'));


        $this->runListener($event);

        // Получает ответ
        $response = $event->getResponse();


        // Код ответа один и тот же
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        // Является объектом ответа
        $this->assertInstanceOf(JsonResponse::class,$response);

        // Сравниваем
        $this->assertJsonStringEqualsJsonString($responseBody,$response->getContent());

    }


    // Провяем 500 ую ошибку и что она логируется
    public function test500IsLoggable(): void
    {
        // loggable - true потому что логируем данный Exception
        $mapping = ExceptionMapping::fromCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        // Статус код стандартный потому что, hidden в строке выше
        $response = Response::$statusTexts[$mapping->getCode()];

        // Ответ в формате джесон
        $responseBody = json_encode(['error' => $response]);

        // Резолвер ожидает что будет вызван метод резолв с аргументом InvalidArgumentException::class который вернет объект
        // ExceptionMapping c указанными ключами и значениями
        $this->resolver->expects($this->once())->method('resolve')
            ->with(InvalidArgumentException::class)->willReturn($mapping);

        // Сериалайзер ожидает метода сериалайз, в который будут переданы класс ошибки с сообщением об ошибке в формате джсон
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($response),JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        //
        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message',$this->anything());

        // Перехватывает ошибку
        $event = $this->createExceptionEvent(new InvalidArgumentException('error message'));


        $this->runListener($event);

        // Получает ответ
        $response = $event->getResponse();


        // Код ответа один и тот же
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        // Является объектом ответа
        $this->assertInstanceOf(JsonResponse::class,$response);

        // Сравниваем
        $this->assertJsonStringEqualsJsonString($responseBody,$response->getContent());


    }

    // Когда наш Резолвер отдает 0 мы должны отдать 500ку
    public function test500DefaultMappingNotFound(): void
    {

        // Статус код стандартный потому что, hidden в строке выше
        $response = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];

        // Ответ в формате джесон
        $responseBody = json_encode(['error' => $response]);

        // Резолвер ожидает что будет вызван метод резолв с аргументом InvalidArgumentException::class который вернет объект
        // ExceptionMapping c указанными ключами и значениями
        $this->resolver->expects($this->once())->method('resolve')
            ->with(InvalidArgumentException::class)->willReturn(null);

        // Сериалайзер ожидает метода сериалайз, в который будут переданы класс ошибки с сообщением об ошибке в формате джсон
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($response),JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        //
        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message',$this->anything());

        // Перехватывает ошибку
        $event = $this->createExceptionEvent(new InvalidArgumentException('error message'));

        // Добавляем тру потому что мы в дебаг режиме
        $this->runListener($event);

        // Получает ответ
        $response = $event->getResponse();


        // Код ответа один и тот же
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        // Является объектом ответа
        $this->assertInstanceOf(JsonResponse::class,$response);

        // Сравниваем
        $this->assertJsonStringEqualsJsonString($responseBody,$response->getContent());


    }

    public function testShowTraceWhenDebug()
    {
        // возвращает объект ExceptionMapping
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);

        // Статус код стандартный потому что, hidden - true в строке выше
        $responseMessage = Response::$statusTexts[$mapping->getCode()];

        // Добавили trace
        $responseBody = json_encode(['error' => $responseMessage, 'trace' => 'something']);

        // Резолвер ожидает что будет вызван метод резолв с аргументом InvalidArgumentException::class который вернет объект
        // ExceptionMapping c указанными ключами и значениями
        $this->resolver->expects($this->once())->method('resolve')
            ->with(InvalidArgumentException::class)
            ->willReturn($mapping);

        // В данном случае изменяем Сериалайзер поскольку мы не знаем какой трейс нам сюда придет, отдать нам нужно
        // его в Something строчка выше.
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($this->callback(
                // Возвращает ErrorResponse при условии что полученное сообщение
                // Вернет ErrorResponse, для того чтобы нам правильно составить ожидание, нам надо взять responseMessage
                function (ErrorResponse $response) use ($responseMessage){
                    /** @var ErrorDebugDetails|object $details */
                    $details = $response->getDetails();
                    // Возвращаем тру или фолс если вернет фолс тест завалится если тру тест продолжится
                    // Сравниваем наше сообщение и что трейс вообще пришел, то есть он не пустой
                    return $response->getMessage() == $responseMessage &&
                        // Проверяем то что детали полученного ответа объект ErrorDebugDetails
                        $details instanceof ErrorDebugDetails &&
                        !empty($details->getTrace());
                }),JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Перехватывает ошибку
        $event = $this->createExceptionEvent(new InvalidArgumentException('error message'));

        // Добавляем тру, для включения дебага, который в верхней строке будет проходить проверку на трейс
        $this->runListener($event,true);

        // Код ответа один и тот же
        $this->assertEquals(Response::HTTP_NOT_FOUND, $event->getResponse()->getStatusCode());

        // Является объектом ответа
        $this->assertInstanceOf(JsonResponse::class,$event->getResponse());

        // Сравниваем
        $this->assertJsonStringEqualsJsonString($responseBody,$event->getResponse()->getContent());
    }


    // Нам всегда понадобится вызвать создать Listener, Принять в него Ивент и получить респонс поэтому для сокращения теста
    // Создаем новые метод.
    private function runListener(ExceptionEvent $event,bool $isDebug = false ): void
    {
        $listener = (new ApiExceptionListener($this->resolver,$this->logger,$this->serializer,$isDebug));

        // Принимает ExceptionEvent
        $listener($event);

    }

    // 1. Создается Event, который отправляется в Listener, при том что аргументами Listenera уже переданны моки объектов.

}

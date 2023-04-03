<?php

namespace Tests\Service\Listener;

use App\Exceptions\ValidationException;
use App\Listener\ValidationExceptionListener;
use App\Models\ErrorResponse;
use App\Models\ErrorValidationDetails;
use App\Tests\AbstractTestCase;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationExceptionListenerTest extends AbstractTestCase
{

    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    // Тест который пропускает исключение если оно не относится к ValidationException
    public function testInvokeSkippedWhenNotValidationException(): void
    {
        // В данном тесте дело не дойдет до сериалайзера, удостоверимся что сериалайзер не будет зайдействован
        $this->serializer->expects($this->never())
            ->method('serialize');

        // Создаем ивент
        $event = $this->createExceptionEvent(new \Exception);

        // И запускаем наш листенер, который ничего не возвращает
        (new ValidationExceptionListener($this->serializer))($event);
    }


    // Цель теста проверить что нам в правильном формате возвращаются ошибки,
    public function testInvoke(): void
    {
        // Определим что мы хотим получить на выходе до переформатирования в json
        $serialized = json_encode([
            'message' => 'validation failed',
            'details' => [
                'violations' => [
                    ['field' => 'name','message' => 'error']
                ]
            ]
        ]);

        $event = $this->createExceptionEvent(new ValidationException(new ConstraintViolationList([
            new ConstraintViolation('error',null,[],null,'name',null)
        ])));

        $this->serializer->expects($this->once())
            ->method('serialize')
            // Ожидается что будет передан объект ErrorResponse,
            ->with($this->callback(function (ErrorResponse $response){
                /** @var ErrorValidationDetails|object $details */
                $details = $response->getDetails();

                // Проверяет что свойство детейлс у ErrorResponse экземпляр класса ErrorValidationDetails, то все ок
                // В противном случае заваливает тест
                // Мы ожидаем что в том ErrorResponse который нам передадут, во первых что это будет тип ErrorValidationDetails
                if (!($details instanceof ErrorValidationDetails)){
                    return false;
                }

                // Проверяем что эти детали не пусты, и что приходящее сообщение 'validation failed'
                // Во вторых у него будет одна ошибка и сообщение об ошибке будет ValidationFailed
                $violations = $details->getViolations();
                if (1 !== count($violations) || 'validation failed' !== $response->getMessage()){
                    return false;
                }

                // Рассматриваем только первый violation потому что он у нас в данном случае должен быть только один
                // И мы ожидаем что в первом аргументе
                // И мы ожидаем что первой ошибки поле будет name, а сообщение error
                return 'name' === $violations[0]->getField() && 'error' === $violations[0]->getMessage();
            }),JsonEncoder::FORMAT)
            ->willReturn($serialized);

        (new ValidationExceptionListener($this->serializer))($event);

        $this->assertResponse(Response::HTTP_BAD_REQUEST,$serialized,$event->getResponse());
    }







}

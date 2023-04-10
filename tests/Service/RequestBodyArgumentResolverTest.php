<?php

namespace App\Tests\Service;

use App\ArgumentResolver\RequestBodyArgumentResolver;
use App\Attribute\RequestBody;
use App\Exceptions\RequestBodyConvertException;
use App\Exceptions\ValidationException;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyArgumentResolverTest extends AbstractTestCase
{

    private SerializerInterface $serializer;

    private ValidatorInterface $validator;


    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    // Проверка если у нас нет атрибута RequestBody, то есть мы его не нашли, то он должен вернуть false
    public function testNotSupports(): void
    {
        // Нам потребуется самим сконструировать объект ArgumentMetadata
        // метод resolve принимает 2 аргумента, все что он принимает это объекты (объект Request, объект ArgumentMetadata)
        // поэтому их мы не мокаем, мокаем мы обычно сервисы
        $meta = new ArgumentMetadata('some', null, false,false,
            null);

        // Проверка на то что возвращает пустой массив
        // в качестве аргумента отправляем пустой Request
        // посколько в $meta мы не задали атрибут равный RequestBody он вернет нам пустой массив
        $this->assertEmpty($this->createResolver()->resolve(new Request(),$meta));

    }

    // Тест ошибки при десериализации
    public function testResolveThrowsDeserialize(): void
    {
        $this->expectException(RequestBodyConvertException::class);

        // Нужно задать контент, контент у нас располагается 7ым аргументом,
        $request = new Request([],[],[],[],[],[],'testing content');

        // type - это тип к которому должен быть десериализован объект в теле запроса.
        $meta = new ArgumentMetadata('some', \stdClass::class, false,false,
            // Закомментированные атрибуты нужны в случае если он находит аргумент, но в данном тесте они не нужны
            null,false,[new RequestBody()]);


        $this->serializer->expects($this->once())
        ->method('deserialize')
            // вместо $request->getcontent 'testing content', вместо argument->getType
        ->with('testing content',\stdClass::class,JsonEncoder::FORMAT)
        ->willThrowException(new \Exception());

        $this->createResolver()->resolve($request,$meta);

    }

    // В данном тесте настраиваем наш сериализатор, чтобы он успешно произвел десериализацию,
    // И когда мы дойдем до валидации мы вернем массив
    public function testResolveThrowsWhenValidationFails(): void
    {
        $this->expectException(ValidationException::class);

        $body = ['test' => true];

        $encodeBody = json_encode($body);

        // Нужно задать контент, контент у нас располагается 7ым аргументом,
        $request = new Request([],[],[],[],[],[], $encodeBody);

        // type - это тип к которому должен быть десериализован объект в теле запроса.
        $meta = new ArgumentMetadata('some', \stdClass::class, false,false,
            // Закомментированные атрибуты нужны в случае если он находит аргумент, но в данном тесте они не нужны
            null,false,[new RequestBody()]);

        // В данном случае наш сериализатор должен успешно сериализировать
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($encodeBody,\stdClass::class,JsonEncoder::FORMAT)
            //
            ->willReturn($body);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($body)
            // как раз возвращает как минимум 1 ошибку
            ->willReturn(new ConstraintViolationList([
                // Для того чтобы все работало корректно нам нужно создать несколько аргументов (в данном случае их 6)
                // потому что те что идут дальше прописаны как null
                // В данном случае ConstraintViolationList будет содержать ConstraintViolation так как якобы обнаруженны
                // ошибки при валидации
                new ConstraintViolation('error',null,[],null,'somve',null)
            ]));


        // Когда сериализатор нам вернет ConstraintViolationList с одним аргументом
        // тогда сработает условие if errors > 0 и будет выброшенно исключение
        //
        $this->createResolver()->resolve($request,$meta);

    }

    //
    public function testResolve()
    {
        $body = ['test' => true];

        $encodeBody = json_encode($body);

        // Нужно задать контент, контент у нас располагается 7ым аргументом,
        $request = new Request([],[],[],[],[],[], $encodeBody);

        // type - это тип к которому должен быть десериализован объект в теле запроса.
        $meta = new ArgumentMetadata('some', \stdClass::class, false,false,
            // Закомментированные атрибуты нужны в случае если он находит аргумент, но в данном тесте они не нужны
            null,false,[new RequestBody()]);

        // В данном случае наш сериализатор должен успешно сериализировать
        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with($encodeBody,\stdClass::class,JsonEncoder::FORMAT)
            // приходит json объект сериалайзер это видит и пытается десериализовать в формат stfClass , т.е в $body
            ->willReturn($body);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($body)
            // Когда наше десериализованное $body приходит в валидатор он возвращает нам пустой объект, говоря о том что никаких
            // ошибок здесь нету
            ->willReturn(new ConstraintViolationList([]));

        // Это все запускается через метод resolve и нам чтобы получить возвращаемое значение мы дергаем первый элемент
        // получаемого массива
        $actual = $this->createResolver()->resolve($request,$meta);

        // $actual будет выдано в след виде 0 => array:1 ["test" => true ];
        $this->assertEquals($body,$actual[0]);


    }


    private function createResolver(): RequestBodyArgumentResolver
    {
        return new RequestBodyArgumentResolver($this->serializer,$this->validator);
    }









}

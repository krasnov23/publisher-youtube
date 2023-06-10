<?php

namespace App\Tests\Service;

use App\ArgumentResolver\RequestFileArgumentResolver as RequestFileArgumentResolver;
use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Exceptions\ValidationException;
use App\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestFileArgumentResolverTest extends AbstractTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

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

    public function testResolveThrowsWhenValidationFails(): void
    {
        $this->expectException(ValidationException::class);

        $file = new UploadedFile('path','field',null,UPLOAD_ERR_NO_FILE,true);

        $request = new Request();
        $request->files->add(['field' => $file]);


        // type - это тип к которому должен быть десериализован объект в теле запроса.
        $meta = new ArgumentMetadata('some', \stdClass::class, false,false,
            // Закомментированные атрибуты нужны в случае если он находит аргумент, но в данном тесте они не нужны
            null,false,[new RequestFile('field',[])]);


        $this->validator->expects($this->once())
            ->method('validate')
            ->with($file,[])
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
        $this->createResolver()->resolve($request,$meta);

    }

    public function testResolveThrowsWhenConstraintFails(): void
    {
        $this->expectException(ValidationException::class);

        $constraints = [new NotNull()];
        $file = new UploadedFile('path','field',null,UPLOAD_ERR_NO_FILE,true);

        $request = new Request();
        $request->files->add(['field' => $file]);


        // type - это тип к которому должен быть десериализован объект в теле запроса.
        $meta = new ArgumentMetadata('some', \stdClass::class, false,false,
            // Закомментированные атрибуты нужны в случае если он находит аргумент, но в данном тесте они не нужны
            null,false,[new RequestFile('field',$constraints)]);


        $this->validator->expects($this->once())
            ->method('validate')
            ->with($file,$constraints)
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
        $this->createResolver()->resolve($request,$meta);

    }



    public function testResolve()
    {
        $file = new UploadedFile('path','field',null,UPLOAD_ERR_NO_FILE,true);

        $request = new Request();
        $request->files->add(['field' => $file]);

        // type - это тип к которому должен быть десериализован объект в теле запроса.
        $meta = new ArgumentMetadata('some', \stdClass::class, false,false,
            // Закомментированные атрибуты нужны в случае если он находит аргумент, но в данном тесте они не нужны
            null,false,[new RequestFile('field',[])]);


        $this->validator->expects($this->once())
            ->method('validate')
            ->with($file,[])
            // Когда наше десериализованное $body приходит в валидатор он возвращает нам пустой объект, говоря о том что никаких
            // ошибок здесь нету
            ->willReturn(new ConstraintViolationList([]));

        // Это все запускается через метод resolve и нам чтобы получить возвращаемое значение мы дергаем первый элемент
        // получаемого массива
        $actual = $this->createResolver()->resolve($request,$meta);


        $this->assertEquals($file,$actual[0]);


    }

    private function createResolver(): RequestFileArgumentResolver
    {
        return new RequestFileArgumentResolver($this->validator);
    }



}
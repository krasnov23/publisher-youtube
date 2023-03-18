<?php

namespace App\Tests\Service\ExceptionHandler;

use App\Exceptions\BookCategoryNotFoundException;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\LogicException;

class ExceptionMappingResolverTest extends AbstractTestCase
{

    //Проверка на то что выкидывает исключение
    public function testResolveThrowsExceptionOnEmptyCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ExceptionMappingResolver(['someClass' => ['code' => '', 'hidden' => true]]);

    }

    // Проверка на то что метод resolve может быть null
    public function testResolveReturnNull(): void
    {

        $exception = new ExceptionMappingResolver([BookCategoryNotFoundException::class => ['code' => 404,'hidden' => true,
            'loggable' => false]]);
        $this->assertNull($exception->resolve(InvalidArgumentException::class));

    }

    // Проверка что метод действительно выдает одну из наших ошибок
    public function testResolveReturnTheSameClass(): void
    {
        // В данном случае сравниваем только номер кода по причине того что если будем в value впихивать полноценный массив он не сравнит
        // его так как возвращает класс ExceptionMapping
        $exception = new ExceptionMappingResolver([BookCategoryNotFoundException::class => ['code' => 404]]);

        $resolve = $exception->resolve(BookCategoryNotFoundException::class);

        $this->assertEquals(404,$resolve->getCode());

    }

    public function testResolvesWithParentClass(): void
    {
        // Проверяем что InvalidArgumentException subclass LogicException то есть наследуется от него
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500]]);

        $mapping = $resolver->resolve(InvalidArgumentException::class);

        $this->assertEquals(500, $mapping->getCode());
    }


    public function testResolverExceptionHasHidenAttr()
    {
        $resolver = new ExceptionMappingResolver([BookCategoryNotFoundException::class => ['code' => 404,'hidden' => false]]);

        $mapping = $resolver->resolve(BookCategoryNotFoundException::class);

        $this->assertSame(false , $mapping->isHidden());


    }












}

<?php

namespace App\Tests\Service;

use App\Entity\BookCategory;
use App\Models\BookCategoryListItem;
use App\Models\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Service\BookCategoryService;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class BookCategoryServiceTest extends TestCase
{

    public function testGetCategories(): void
    {
        // Создал макет репозитория (пустышку)
        $repository = $this->createMock(BookCategoryRepository::class);

        // Настраиваем метод и ожидаем что он будет вызван один раз, сам метод будет findBy
        // и репозиторий будет упорядочен по алфавиту и вернет массив в котором будет экземпляр класса BookCategory
        $repository->expects($this->once())->method('findBy')->with([],['title' => Criteria::ASC])
            ->willReturn([(new BookCategory())->setId(7)->setTitle('Test')->setSlug('test')]);


        $service = new BookCategoryService($repository);

        $expected = new BookCategoryListResponse([new BookCategoryListItem(7,'Test','test')]);

        $this->assertEquals($expected,$service->getCategories());


    }
}

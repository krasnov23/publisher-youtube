<?php

namespace App\Tests\Service;

use App\Entity\BookCategory;
use App\Models\BookCategoryModel;
use App\Models\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Service\BookCategoryService;
use App\Tests\AbstractTestCase;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookCategoryServiceTest extends AbstractTestCase
{
    public function testGetCategories(): void
    {
        $category = (new BookCategory())->setTitle('Test')->setSlug('test');

        $this->setEntityId($category, 7);

        // Создал макет репозитория (пустышку)
        $repository = $this->createMock(BookCategoryRepository::class);

        $slugger = $this->createMock(SluggerInterface::class);

        // Настраиваем метод и ожидаем что он будет вызван один раз, сам метод будет findBy
        // и репозиторий будет упорядочен по алфавиту и вернет массив в котором будет экземпляр класса BookCategory
        $repository->expects($this->once())->method('findAllSortedByAlphabet')
            ->willReturn([$category]);

        $service = new BookCategoryService($repository,$slugger);

        $expected = new BookCategoryListResponse([new BookCategoryModel(7, 'Test', 'test')]);

        $this->assertEquals($expected, $service->getCategories());
    }


}

<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Exceptions\BookCategoryNotFoundException;
use App\Models\BookListItem;
use App\Models\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BookServiceTest extends AbstractTestCase
{

    public function testGetBooksByCategoryNotFound(): void
    {
        $bookRepository = $this->createMock(BookRepository::class);

        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);

        // Тест на то что при методе фаинд с айди 130 не будет найдена данная категория книг
        // Ожидает что при выбросе айди = 130, mock выбросит исключение
        $bookCategoryRepository->expects($this->once())
            ->method("find")
            ->with(130)
            ->willThrowException(new BookCategoryNotFoundException());

        $this->expectException(BookCategoryNotFoundException::class);

        (new BookService($bookRepository, $bookCategoryRepository))->getBookByCategory(130);

    }

    public function testGetBooksByCategory(): void
    {

        $bookRepository = $this->createMock(BookRepository::class);

        // Эмулирует метод файндбайкатегори с айди равным 130, и ожидает что он вернет нам результат метода createBookEntity
        // Эмулирует метод который находится в BookRepository, мы хотим вернуть класс Book, но поскольку это занимает у нас несколько
        // строчек мы создали для этого отдельный метод (createBookEntity)
        $bookRepository->expects($this->once())
            // Ожидает что будет вызван метод который находится в bookrepository
            ->method('findBooksByCategoryId')
            // c айди категорией 130
            ->with(130)
            // и вернет массив из книг( в данном случае одной книги которая будет подходить под категорию 130)
            ->willReturn([$this->createBookEntity()]);


        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);


        $bookCategoryRepository->expects($this->once())
            // Ищет категорию с айди 130
            ->method('find')
            ->with(130)
            // Возвращает BookCategory - в данном случае пустой так как нам не важно что-то кроме айди конкретной категории
            ->willReturn(new BookCategory());

        // Отправляет наши значения в BookService и должен дать нам на выходе то что будет указанно в expected
        $service = new BookService($bookRepository, $bookCategoryRepository);

        // Для проверки конечного результата создаем метод который будет указан ниже
        $expected = new BookListResponse([$this->createBookItemModel()]);


        $this->assertEquals($expected, $service->getBookByCategory(130));

    }

    private function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug("test-book")
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('tester.jpg')
            // В данном случае пустой, такой же как и сущность
            ->setCategories(new ArrayCollection())
            ->setPublicationData(new \DateTime('2020-10-10'));

        $this->setEntityId($book,123,);

        return $book;
    }

    private function createBookItemModel(): BookListItem
    {
        // BookListItem не имеет сеткатегорис так как это уже переписанная сущность подобранная под определенную категорию
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug("test-book")
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('tester.jpg')
            ->setPublicationData(1602288000);

    }

}

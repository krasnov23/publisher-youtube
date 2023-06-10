<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Models\Author\PublishBookRequest;
use App\Repository\BookRepository;
use App\Service\BookPublishService;
use App\Tests\AbstractTestCase;
use DateTimeImmutable;

class BookPublishServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);
    }

    public function testPublish(): void
    {
        $datetime = new DateTimeImmutable('2020-10-10');
        $publishBookRequest = (new PublishBookRequest())->setDate($datetime);

        $book = (new Book());

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('save')
            ->with($book,true);

        (new BookPublishService($this->bookRepository))->publish(1,$publishBookRequest);

        $this->assertEquals($datetime,$book->getPublicationData());
    }

    public function testUnpublish(): void
    {
        $book = (new Book())->setPublicationData(new DateTimeImmutable('2020-10-10'));

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('save')
            ->with($book,true);

        (new BookPublishService($this->bookRepository))->unpublish(1);

        $this->assertEquals(null,$book->getPublicationData());
    }

}
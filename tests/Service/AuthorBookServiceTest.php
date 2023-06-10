<?php

namespace App\tests\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Entity\UserApi;
use App\Exceptions\BookAlreadyExistsException;
use App\Mapper\BookMapper;
use App\Models\Author\BookAuthorDetails;
use App\Models\Author\BookFormatOptions;
use App\Models\Author\BookListItem;
use App\Models\Author\BookListResponse;
use App\Models\Author\CreateBookRequest;
use App\Models\Author\UpdateBookRequest;
use App\Models\Author\UploadCoverResponse;
use App\Models\BookCategoryModel;
use App\Models\BookDetails;
use App\Models\BookFormatModel;
use App\Models\IdResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookFormatRepository;
use App\Repository\BookRepository;
use App\Service\AuthorBookService;
use App\Service\UploadService;
use App\Tests\AbstractTestCase;
use DateTimeImmutable;
use App\Service\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\AbstractUnicodeString;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class AuthorBookServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    private BookCategoryRepository $bookCategoryRepository;

    private SluggerInterface $slugger;

    private BookFormatRepository $bookFormatRepository;

    private UploadService $uploadService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->bookFormatRepository = $this->createMock(BookFormatRepository::class);
        $this->uploadService = $this->createMock(UploadService::class);
    }

    public function testUploadCoverRemoveOldImage(): void
    {

        $book = (new Book())->setImage("http://localhost/old.jpg");
        $this->setEntityId($book,1);

        $file = (new UploadedFile('path','field',null,UPLOAD_ERR_NO_FILE,true));

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('save')
            ->with($book,true);

        $this->uploadService->expects($this->once())
            ->method('uploadBookFile')
            ->with(1,$file)
            ->willReturn('http://localhost/new.jpg');

        $this->uploadService->expects($this->once())
            ->method("deleteBookFile")
            ->with(1,"old.jpg");

        $this->assertEquals(new UploadCoverResponse("http://localhost/new.jpg"),
            $this->createService()->uploadCover(1,$file));

    }

    public function testUploadCover()
    {
        $book = (new Book())->setImage(null);
        $this->setEntityId($book,1);

        $file = (new UploadedFile('path','field',null,UPLOAD_ERR_NO_FILE,true));

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('save')
            ->with($book,true);

        $this->uploadService->expects($this->once())
            ->method('uploadBookFile')
            ->with(1,$file)
            ->willReturn('http://localhost/new.jpg');

        $this->assertEquals(new UploadCoverResponse("http://localhost/new.jpg"),
            $this->createService()->uploadCover(1,$file));
    }

    public function testDeleteBook(): void
    {
        $book = new Book();

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('remove')
            ->with($book,true);

        $this->createService()->deleteBook(1);
    }

    public function testGetBook()
    {
        $category = MockUtils::createBookCategory();
        $this->setEntityId($category,1);

        $formats = MockUtils::createBookFormat();
        $this->setEntityId($formats,1);

        $book = MockUtils::createBook()->setCategories(new ArrayCollection([$category]));
        $this->setEntityId($book,1);

        $bookLink = MockUtils::createBookFormatLink($book,$formats);
        $book->setFormats(new ArrayCollection([$bookLink]));

        $bookDetails = (new BookAuthorDetails())
            ->setId(1)
            ->setTitle('Test Book')
            ->setImage('http://localhost.png')
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationDate($book->getPublicationData()->getTimestamp())
            ->setAuthors(['Tester'])
            ->setCategories(BookMapper::mapCategories($book))
            ->setFormats(BookMapper::mapFormats($book))
            ->setSlug('test-book');

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->assertEquals($bookDetails,$this->createService()->getBook(1));
    }

    public function testGetBooks(): void
    {
        $user = new UserApi();

        $book = MockUtils::createBook()->setUser($user);
        $this->setEntityId($book, 1);

        $this->bookRepository->expects($this->once())
            ->method('findUserBooks')
            ->with($user)
            ->willReturn([$book]);

        $bookItem = (new BookListItem())->setId(1)
            ->setImage('http://localhost.png')
            ->setTitle('Test Book')
            ->setSlug('test-book');

        $this->assertEquals(new BookListResponse([$bookItem]),$this->createService()->getBooks($user));
    }

    public function testCreateBook(): void
    {
        $payload = (new CreateBookRequest())->setTitle('New Book');
        $user = new UserApi();
        $expectedBook = (new Book())->setTitle('New Book')
            ->setSlug('new-book')->setUser($user)->setPublicationData(new DateTimeImmutable('2020-10-10'));

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Book')
            ->willReturn(new UnicodeString('new-book'));

        $this->bookRepository->expects($this->once())
            ->method('existsBySlug')
            ->with('new-book')
            ->willReturn(false);

        $this->bookRepository->expects($this->once())
            ->method('save')
            ->with($expectedBook,true)
            ->will($this->returnCallback(function (Book $book){
                $this->setEntityId($book,1);
            }));

        $expectedResult = (new IdResponse(1));

        $this->assertEquals($expectedResult,$this->createService()->createBook($payload,$user));
    }

    public function testCreateBookSlugExistsException(): void
    {
        $this->expectException(BookAlreadyExistsException::class);

        $payload = (new CreateBookRequest())->setTitle('New Book');
        $user = new UserApi();

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Book')
            ->willReturn(new UnicodeString('new-book'));

        $this->bookRepository->expects($this->once())
            ->method('existsBySlug')
            ->with('new-book')
            ->willReturn(true);

        $this->createService()->createBook($payload,$user);
    }

    public function testUpdateBookSlugException(): void
    {
        $this->expectException(BookAlreadyExistsException::class);

        $payload = (new UpdateBookRequest())->setTitle('New Book');
        $book = (new Book());
        $this->setEntityId($book,1);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Book')
            ->willReturn(new UnicodeString('new-book'));

        $this->bookRepository->expects($this->once())
            ->method('existsBySlug')
            ->with('new-book')
            ->willReturn(true);

        $this->createService()->updateBook(1,$payload);

    }

    public function testUpdateBook(): void
    {
        $bookToBookFormat = (new BookToBookFormat());
        $book = (new Book())->setFormats(new ArrayCollection([$bookToBookFormat]));
        $this->setEntityId($book,1);

        $category = MockUtils::createBookCategory();
        $this->setEntityId($category,1);

        $payload = (new UpdateBookRequest())->setTitle('New Book')
                ->setAuthors(['Tester'])->setIsbn('isbn')->setCategories([1])
                ->setFormats([(new BookFormatOptions())->setId(1)->setPrice(123.5)->setDiscountPercent(5)])
                ->setDescription('description');

        $bookFormat = MockUtils::createBookFormat();
        $this->setEntityId($bookFormat,1);

        $bookToBookFormatNew = (new BookToBookFormat())->setBook($book)
                        ->setPrice(123.5)->setDiscountPercent(5)
                        ->setFormat($bookFormat);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Book')
            ->willReturn(new UnicodeString('new-book'));

        $this->bookRepository->expects($this->once())
            ->method('existsBySlug')
            ->with('new-book')
            ->willReturn(false);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method("removeBookFormatReference")
            ->with($bookToBookFormat);

        $this->bookFormatRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($bookFormat);

        $this->bookRepository->expects($this->once())
            ->method("saveBookFormatReference")
            ->with($bookToBookFormatNew,true);

        $this->bookCategoryRepository->expects($this->once())
            ->method('findBookCategoriesByIds')
            ->with([1])
            ->willReturn([$category]);


        $this->createService()->updateBook(1,$payload);

    }
    

    private function createService(): AuthorBookService
    {
        return (new AuthorBookService($this->bookRepository,$this->bookCategoryRepository,
            $this->slugger,$this->bookFormatRepository,$this->uploadService));
    }




}
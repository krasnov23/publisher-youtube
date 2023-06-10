<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Exceptions\BookAlreadyExistsException;
use App\Mapper\BookMapper;
use App\Models\Author\BookAuthorDetails;
use App\Models\Author\BookFormatOptions;
use App\Models\Author\BookListItem;
use App\Models\Author\BookListResponse;
use App\Models\Author\CreateBookRequest;
use App\Models\Author\UpdateBookRequest;
use App\Models\Author\UploadCoverResponse;
use App\Models\BookDetails;
use App\Models\IdResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookFormatRepository;
use App\Repository\BookRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookService
{

    public function __construct(
        private BookRepository $bookRepository,
        private BookCategoryRepository $bookCategoryRepository,
        private SluggerInterface $slugger,
        private BookFormatRepository $bookFormatRepository,
        private UploadService $uploadService
        //private EventDispatcherInterface $eventDispatcher
    )
    {
    }


    // Получаем id книги и загруженный файл.
    public function uploadCover(int $id, UploadedFile $uploadedFile): UploadCoverResponse
    {
        $book = $this->bookRepository->getBookById($id);

        $oldImage = $book->getImage();

        $link = $this->uploadService->uploadBookFile($id,$uploadedFile);

        $book->setImage($link);

        $this->bookRepository->save($book,true);

        // Если у нас уже есть загруженная в папку картинка значит нам необходимо ее удалить, для того чтобы не занимать
        // лишнее место
        if (null !== $oldImage)
        {
            $this->uploadService->deleteBookFile($book->getId(), basename($oldImage));
        }

        return new UploadCoverResponse($link);
    }

    // Получение всех книг текущего пользователя
    public function getBooks(UserInterface $user): BookListResponse
    {
        return new BookListResponse(
            array_map([$this,'map'],$this->bookRepository->findUserBooks($user))
        );
    }

    // Получение деталей книги автора
    public function getBook(int $id): BookAuthorDetails
    {
        $book = $this->bookRepository->getBookById($id);

        $bookDetails = (new BookAuthorDetails())
            ->setIsbn($book->getIsbn())
            ->setDescription($book->getDescription())
            ->setFormats(BookMapper::mapFormats($book))
            ->setCategories(BookMapper::mapCategories($book));

        return BookMapper::map($book,$bookDetails);
    }

    // Создание книги
    public function createBook(CreateBookRequest $request, UserInterface $user): IdResponse
    {
        // Генерируем слаг
        $slug = $this->slugOrThrow($request->getTitle());

        $book = (new Book())
                ->setTitle($request->getTitle())
                ->setSlug($slug)
                ->setPublicationData(new DateTimeImmutable('2020-10-10'))
                ->setUser($user);

        $this->bookRepository->save($book,true);

        return new IdResponse($book->getId());
    }

    // Данный метод будет написан так что когда нам будет приходить payload, он будет затирать все поля, если в каком-то
    // поле придет null или какое-то поле вообще не придет, соответстующее поле в базе будет зануленно.
    // Принимает id и payload
    public function updateBook(int $id, UpdateBookRequest $updateBookRequest): void
    {
        $book = $this->bookRepository->getBookById($id);

        $title = $updateBookRequest->getTitle();

        // если title из реквеста не пустой устанавливаем его, то есть если в реквесте пустое название
        // не изменяем название книги.
        if (!empty($title))
        {
            $book->setTitle($title)->setSlug($this->slugOrThrow($title));
        }

        // Удаляются старые связи до форматов
        foreach ($book->getFormats() as $format)
        {
            $this->bookRepository->removeBookFormatReference($format);
        }

        $formats = array_map(function (BookFormatOptions $options) use ($book): BookToBookFormat {
            $format = (new BookToBookFormat())
                ->setPrice($options->getPrice())
                ->setDiscountPercent($options->getDiscountPercent())
                ->setBook($book)
                ->setFormat($this->bookFormatRepository->getById($options->getId()));

            $this->bookRepository->saveBookFormatReference($format,true);

            return $format;
        }, $updateBookRequest->getFormats());


        $book->setAuthors($updateBookRequest->getAuthors())
            ->setIsbn($updateBookRequest->getIsbn())
            ->setDescription($updateBookRequest->getDescription())
            ->setCategories(new ArrayCollection(
                $this->bookCategoryRepository->findBookCategoriesByIds($updateBookRequest->getCategories())
            ))
            ->setFormats(new ArrayCollection($formats));

        $this->bookRepository->save($book,true);
    }

    // Удаление определенной книги.
    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getBookById($id);

        $this->bookRepository->remove($book,true);
    }

    private function slugOrThrow(string $title): string
    {
        $slug = $this->slugger->slug($title);

        if ($this->bookRepository->existsBySlug($slug))
        {
            throw new BookAlreadyExistsException();
        }

        return $slug;
    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setTitle($book->getTitle());
    }


}
<?php

namespace App\Service;

use App\Entity\Book;
use App\Exceptions\BookAlreadyExistsException;
use App\Models\Author\BookListItem;
use App\Models\Author\BookListResponse;
use App\Models\Author\CreateBookRequest;
use App\Models\Author\UploadCoverResponse;
use App\Models\IdResponse;
use App\Repository\BookRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookService
{

    public function __construct(
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
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

        $this->em->flush();

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

    // Создание книги
    public function createBook(CreateBookRequest $request, UserInterface $user): IdResponse
    {
        // Генерируем слаг
        $slug = $this->slugger->slug($request->getTitle());

        if ($this->bookRepository->existsBySlug($slug))
        {
            throw new BookAlreadyExistsException();
        }

        $book = (new Book())
                ->setMeap(false)
                ->setTitle($request->getTitle())
                ->setSlug($slug)
                ->setPublicationData(new DateTimeImmutable('2020-10-10'))
                ->setUser($user);

        $this->em->persist($book);
        $this->em->flush();

        return new IdResponse($book->getId());
    }

    // Удаление определенной книги.
    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getBookById($id);

        $this->em->remove($book);
        $this->em->flush();
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
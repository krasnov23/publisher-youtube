<?php

namespace App\Service;

use App\Entity\Book;
use App\Exceptions\BookAlreadyExistsException;
use App\Models\Author\BookListItem;
use App\Models\Author\BookListResponse;
use App\Models\Author\CreateBookRequest;
use App\Models\Author\PublishBookRequest;
use App\Models\IdResponse;
use App\Repository\BookRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorService
{

    public function __construct(
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
        private Security $security
    )
    {
    }

    // Метод обновляет дату публикации
    public function publish(int $id,PublishBookRequest $publishBookRequest): void
    {
        $this->setPublicationDate($id,$publishBookRequest->getDate());
    }

    // Метод снятия с публикации
    public function unpublish(int $id)
    {
        $this->setPublicationDate($id,null);
    }

    // Получение всех книг текущего пользователя
    public function getBooks(): BookListResponse
    {
        $user = $this->security->getUser();

        return new BookListResponse(
            array_map([$this,'map'],$this->bookRepository->findUserBooks($user))
        );
    }

    // Создание книги
    public function createBook(CreateBookRequest $request): IdResponse
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
                ->setPublicationData(new \DateTimeImmutable('2020-10-10'))
                ->setUser($this->security->getUser());

        $this->em->persist($book);
        $this->em->flush();

        return new IdResponse($book->getId());
    }

    // Удаление определенной книги.
    public function deleteBook(int $id): void
    {
        $user = $this->security->getUser();

        $book = $this->bookRepository->getUserBookById($id, $user);

        $this->em->remove($book);
        $this->em->flush();
    }

    private function setPublicationDate(int $book, ?DateTimeInterface $dateTime): void
    {
        $book = $this->bookRepository->getUserBookById($book, $this->security->getUser());

        $book->setPublicationData($dateTime);

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
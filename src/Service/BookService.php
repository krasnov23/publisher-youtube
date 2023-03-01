<?php

namespace App\Service;

use App\Entity\Book;
use App\Exceptions\BookCategoryNotFoundException;
use App\Models\BookListItem;
use App\Models\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;

class BookService
{
    public function __construct(private BookRepository $bookRepository, private BookCategoryRepository $bookCategoryRepository)
    {
    }

    public function getBookByCategory(int $categoryId): BookListResponse
    {
        $category = $this->bookCategoryRepository->find($categoryId);

        if ($category === null)
        {
            throw new BookCategoryNotFoundException();
        }

        // Берет наш объект бук из тех книг который подходят под определенную категорию
        // и переписывает их в массив из объектов класса BookListItem
        $mapping = array_map([$this,'map'],$this->bookRepository->findBooksByCategoryId($categoryId));

        return new BookListResponse($mapping);

    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationData($book->getPublicationData()->getTimestamp()
            )
            ;


    }

}
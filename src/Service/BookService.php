<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exceptions\BookCategoryNotFoundException;
use App\Mapper\BookMapper;
use App\Models\BookCategoryModel;
use App\Models\BookDetails;
use App\Models\BookFormatModel;
use App\Models\BookListItem;
use App\Models\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\Collection;

class BookService
{
    public function __construct(private BookRepository           $bookRepository,
                                private BookCategoryRepository   $bookCategoryRepository,
                                private RatingService            $ratingService)
    {
    }

    public function getBookByCategory(int $categoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existsById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        // В данном случае берет массив из книг найденных по категории и каждую из книг отправляем в метод мап
        // где уже метод перемапливает в модель BookListItem
        $mapping = array_map(fn (Book $book) => BookMapper::map($book,new BookListItem()) ,
            $this->bookRepository->findPublishedBooksByCategoryId($categoryId));


        return new BookListResponse($mapping);
    }

    public function getBookById(int $id): BookDetails
    {
        // Ищем книгу по Id
        $book = $this->bookRepository->getPublishedById($id);
        $rating = $this->ratingService->calcReviewRatingForBook($id);
        $categories = BookMapper::mapCategories($book);
        $formats = BookMapper::mapFormats($book);

        return BookMapper::map($book,new BookDetails())
            ->setRating($rating->getRating())
            ->setReviews($rating->getTotal())
            ->setFormats($formats)
            ->setCategories($categories);

    }



}

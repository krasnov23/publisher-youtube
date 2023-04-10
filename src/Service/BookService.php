<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exceptions\BookCategoryNotFoundException;
use App\Models\BookCategoryModel;
use App\Models\BookDetails;
use App\Models\BookFormatModel;
use App\Models\BookListItem;
use App\Models\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\Collection;

class BookService
{
    public function __construct(private BookRepository $bookRepository,
                                private BookCategoryRepository $bookCategoryRepository,
                                private ReviewRepository $reviewRepository)
    {
    }

    public function getBookByCategory(int $categoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existsById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        // Берет наш объект бук из тех книг который подходят под определенную категорию
        // и переписывает их в массив из объектов класса BookListItem
        // В данном случае $this это каждый объект найденного по категории репозитория то есть объекты класса Book
        //
        $mapping = array_map([$this, 'map'], $this->bookRepository->findBooksByCategoryId($categoryId));


        return new BookListResponse($mapping);
    }

    public function getBookById(int $id): BookDetails
    {
        // Ищем книгу по Id
        $book = $this->bookRepository->getById($id);

        // Считаем количество отзывов у этой книги по ID
        $reviews = $this->reviewRepository->countByBookId($id);

        $rating = 0;

        if ($reviews > 0)
        {
            // Считает сумму рейтингов по id книги
            $rating = $this->reviewRepository->getBookTotalRatingSum($id) / $reviews;
        }

        //
        $formats = $this->mapFormats($book->getFormats());

        $categories = $book->getCategories()
                ->map(fn (BookCategory $bookCategory) => (new BookCategoryModel(
                    $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug())));

        return (new BookDetails())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationData($book->getPublicationData()->getTimestamp())
            // Суммарный рейтинг всех комментариев деленный на количество отзывов
            ->setRating($rating)
            ->setReviews($reviews)
            ->setFormats($formats)
            ->setCategories($categories->toArray());

    }

    /**
     * @param Collection<BookToBookFormat> $formats
     * @return array
     */
    private function mapFormats(Collection $formats): array
    {
        return $formats->map(fn(BookToBookFormat $formatJoin) => (new BookFormatModel())
            ->setId($formatJoin->getFormat()->getId())
            ->setTitle($formatJoin->getFormat()->getTitle())
            ->setDescription($formatJoin->getFormat()->getDescription())
            ->setComment($formatJoin->getFormat()->getComment())
            ->setPrice($formatJoin->getPrice())
            ->setDiscountPercent($formatJoin->getDiscountPercent())
        );
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

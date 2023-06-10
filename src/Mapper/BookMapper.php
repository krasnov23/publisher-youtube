<?php

namespace App\Mapper;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Models\Author\BookAuthorDetails;
use App\Models\BookCategoryModel;
use App\Models\BookDetails;
use App\Models\BookFormatModel;
use App\Models\BookListItem;
use Doctrine\Common\Collections\Collection;

class BookMapper
{

    // Метод заменяющий нам метод поиска книг по категориям и его маппинг и частично заменяющий нам возврат модели
    // BookDetails в котором кроме деталей книги также задаются рейтинг, количество отзывов, букформат итд
    public static function map(Book $book,BookDetails|BookListItem|BookAuthorDetails $model):
    BookDetails|BookListItem|BookAuthorDetails
    {
        $publicationDate = $book->getPublicationData();

        if (null !== $publicationDate)
        {
            $publicationDate = $publicationDate->getTimestamp();
        }

        return $model->setId($book->getId())
        ->setTitle($book->getTitle())
        ->setSlug($book->getSlug())
        ->setImage($book->getImage())
        ->setAuthors($book->getAuthors())
        ->setPublicationDate($publicationDate);
        // Суммарный рейтинг всех комментариев деленный на количество отзывов

    }

    public static function mapCategories(Book $book): array
    {
        return $book->getCategories()
            ->map(fn (BookCategory $bookCategory) => (new BookCategoryModel(
                $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug())))
                ->toArray();

    }

    /**
     * @return BookFormatModel[]
     */
    public static function mapFormats(Book $book): array
    {

        return $book->getformats()->map(fn(BookToBookFormat $formatJoin) => (new BookFormatModel())
            ->setId($formatJoin->getFormat()->getId())
            ->setTitle($formatJoin->getFormat()->getTitle())
            ->setDescription($formatJoin->getFormat()->getDescription())
            ->setComment($formatJoin->getFormat()->getComment())
            ->setPrice($formatJoin->getPrice())
            ->setDiscountPercent($formatJoin->getDiscountPercent()))->toArray();

    }






}
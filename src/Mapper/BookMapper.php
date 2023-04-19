<?php

namespace App\Mapper;

use App\Entity\Book;
use App\Models\BookDetails;
use App\Models\BookListItem;

class BookMapper
{

    // Метод заменяющий нам метод поиска книг по категориям и его маппинг и частично заменяющий нам возврат модели
    // BookDetails в котором кроме деталей книги также задаются рейтинг, количество отзывов, букформат итд
    public static function map(Book $book,BookDetails|BookListItem $model): BookDetails|BookListItem
    {
        return $model->setId($book->getId())
        ->setTitle($book->getTitle())
        ->setSlug($book->getSlug())
        ->setImage($book->getImage())
        ->setAuthors($book->getAuthors())
        ->setMeap($book->isMeap())
        ->setPublicationData($book->getPublicationData()->getTimestamp());
        // Суммарный рейтинг всех комментариев деленный на количество отзывов

    }

}
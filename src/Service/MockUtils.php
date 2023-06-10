<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Entity\Review;
use App\Entity\UserApi;
use Doctrine\Common\Collections\ArrayCollection;
use DateTimeImmutable;

class MockUtils
{

    public static function createUser(): UserApi
    {
        return (new UserApi())
            ->setEmail('vasya@localhost.local')
            ->setFirstName('Vasya')
            ->setLastName('Testerov')
            ->setRoles(['ROLE_AUTHOR'])
            ->setPassword('password');

    }

    public static function createBookCategory(): BookCategory
    {
        return (new BookCategory())
            ->setTitle('Devices')
            ->setSlug('devices');
    }

    public static function createBook(): Book
    {
        return (new Book())
            ->setTitle('Test Book')
            ->setImage('http://localhost.png')
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationData(new DateTimeImmutable('2020-10-10'))
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([]))
            ->setSlug('test-book');

    }


    public static function createReview(Book $book): Review
    {
        return (new Review())
            ->setAuthor('tester')
            ->setContent('test content')
            ->setCreatedAt(new DateTimeImmutable('2020-10-10'))
            ->setRating(5)
            ->setBook($book);
    }

    public static function createBookFormat(): BookFormat
    {
        return (new BookFormat())
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null);
    }


    public static function createBookFormatLink(Book $book, BookFormat $bookFormat): BookToBookFormat
    {
        return (new BookToBookFormat())
            ->setPrice(123.55)
            ->setDiscountPercent(5)
            ->setBook($book)
            ->setFormat($bookFormat);
    }





}
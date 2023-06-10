<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\Service\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;

// Проверяем что конкретный метод репозитория работает
class BookRepositoryTest extends AbstractRepositoryTest
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->getRepositoryForEntity(Book::class);
    }

    public function testFindBookByCategoryId()
    {
        $user = MockUtils::createUser();
        $this->em->persist($user);

        $devicesCategory = MockUtils::createBookCategory();
        $this->em->persist($devicesCategory);

        for ($i = 0; $i < 5; $i++) {
            $book = MockUtils::createBook()->setUser($user)
                ->setTitle("device-$i")
                ->setCategories(new ArrayCollection([$devicesCategory]));
            $this->em->persist($book);
        }

        $this->em->flush();

        // 5 совпадает с количеством найденных по категории книг
        $this->assertCount(5, $this->bookRepository->findPublishedBooksByCategoryId($devicesCategory->getId()));
    }

    public function createBook(string $title,BookCategory $category): Book
    {
        return (new Book())
            ->setPublicationData(new \DateTimeImmutable())
            ->setAuthors(['author'])
            ->setSlug($title)
            ->setDescription('test description')
            ->setIsbn('123123')
            ->setTitle($title)
            ->setCategories(new ArrayCollection([$category]))
            ->setImage($title .".jpg");
    }


}
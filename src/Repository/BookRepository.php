<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Exceptions\BookNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function saveBookFormatReference(BookToBookFormat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeBookFormatReference(BookToBookFormat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Book[]
     */
    public function findPublishedBooksByCategoryId(int $id): array
    {
        return $this->_em->
        createQuery('SELECT b FROM App\Entity\Book b WHERE :Id MEMBER OF b.categories AND b.publicationData IS NOT NULL')
            ->setParameter('Id',$id)
            ->getResult();

    }

    public function getPublishedById(int $id): Book
    {
        $book = $this->_em->createQuery('SELECT b FROM App\Entity\Book b WHERE b.id = :id AND b.publicationData IS NOT NULL')
                ->setParameter('id',$id)
                ->getOneOrNullResult();

        // В данном случае мы делаем проверку на null в репозитории просто для того чтобы много раз не делать ее в сервисе.
        if (null === $book)
        {
            throw new BookNotFoundException();
        }

        return $book;
    }

    /**
     * @return Book[]
     */
    public function findBooksByIds(array $ids): array
    {
        return $this->_em->createQuery('SELECT b FROM App\Entity\Book b WHERE b.id IN (:ids) AND b.publicationData IS NOT NULL')
            ->setParameter('ids',$ids)
            ->getResult();

    }

    /**
     * @return Book[]
     */
    public function findUserBooks(UserInterface $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    public function getBookById(int $id): Book
    {
        $book = $this->find($id);

        if ($book === null)
        {
            throw new BookNotFoundException();
        }

        return $book;
    }

    public function existsBySlug(string $slug): bool
    {
        return null !== $this->findOneBy(['slug' => $slug]);
    }

    public function existsUserBookById(int $id, UserInterface $user): bool
    {
        return null !== $this->findOneBy(['id' => $id, 'user' => $user]);
    }







//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



}

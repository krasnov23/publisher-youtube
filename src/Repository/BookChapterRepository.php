<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exceptions\ChapterNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookChapter>
 *
 * @method BookChapter|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookChapter|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookChapter[]    findAll()
 * @method BookChapter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookChapterRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookChapter::class);
    }

    use RepositoryModifyTrait;

    public function getBookById(int $id): BookChapter
    {
        $chapter = $this->find($id);

        if ($chapter === null)
        {
            throw new ChapterNotFoundException();
        }

        return $chapter;
    }

    // Получение самого высокого значения для текущей сортировки, для определенной книги для определенного уровня
    public function getMaxSort(Book $book,int $level): int
    {
        // Приводим к инту потому что getSingleScalarResult может дать null
        return (int) $this->_em->createQuery('SELECT MAX(c.sort) FROM App\Entity\BookChapter c WHERE c.book = :book 
        AND c.level = :level')
            ->setParameter('book',$book)
            ->setParameter('level',$level)
            ->getSingleScalarResult();

    }

    // Увеличение всех последовательных элементов после определенного на определенном уровне
    public function increaseSortFrom(int $sortStart, Book $book,int $level,int $sortStep = 1 ): void
    {
        $this->_em->createQuery('UPDATE App\Entity\BookChapter c SET c.sort = c.sort + :sortStep WHERE c.sort >= :sortStart
        AND c.book = :book AND c.level = :level')
            ->setParameter('book',$book)
            ->setParameter('sortStart',$sortStart)
            ->setParameter('level',$level)
            ->setParameter('sortStep',$sortStep)
            ->execute();
    }

    // Метод для получения сортированного оглавления книги, для этого нам надо отсортировать главы сначала по уровню а потом
    // по значению сортировки
    /**
     * @return BookChapter[]
     */
    public function findSortedChapterByBook(Book $book): array
    {
        return $this->findBy(['book' => $book],['level' => Criteria::ASC, 'sort' => Criteria::ASC]);
    }


    public function existsBySlug(string $slug): bool
    {
        return null !== $this->findOneBy(['slug' => $slug]);
    }



//    /**
//     * @return BookChapter[] Returns an array of BookChapter objects
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

//    public function findOneBySomeField($value): ?BookChapter
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

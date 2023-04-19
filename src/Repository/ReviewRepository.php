<?php

namespace App\Repository;

use App\Entity\Review;
use Countable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Traversable;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @return Traversable&Countable
     */
    // Метод для получения страницы c комментарием ограниченный страницей комментария и
    public function getPageByBookId(int $id,int $offset,int $limit)
    {
        $query = $this->_em->createQuery('SELECT r FROM App\Entity\Review r WHERE r.book = :id ORDER BY r.createdAt DESC')
        // Установили первый результат, установили количество отзывов на страницу
        ->setParameter('id',$id)->setFirstResult($offset)->setMaxResults($limit);

        // false - отвечает за то что нас не интересуют связи с другими таблицами
        return new Paginator($query, false);
    }

    public function countByBookId(int $id): int
    {
        // Считает сущности Review у книги по определенному id
        return $this->count(['book' => $id]);

    }

    // Метод считающий рейтинг книги
    public function getBookTotalRatingSum(int $id): int
    {
        // Складывает сумму рейтингов по id книги
        return (int)$this->_em->createQuery('SELECT SUM(r.rating) FROM App\Entity\Review r WHERE r.book = :id')
            ->setParameter('id',$id)
            // Обычно он возвращает массив спецэфической формы, когда мы хотим получить именно одно значение скалярное (типа сумма)
            // позволяет получить с первой колонки первый результат
            ->getSingleScalarResult();
    }

    public function save(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Review $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Review[] Returns an array of Review objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Review
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

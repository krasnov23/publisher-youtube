<?php

namespace App\Repository;

use App\Entity\BookFormat;
use App\Exceptions\BookFormatNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookFormat>
 *
 * @method BookFormat|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookFormat|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookFormat[]    findAll()
 * @method BookFormat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookFormatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookFormat::class);
    }

    use RepositoryModifyTrait;

    public function getById(int $id): BookFormat
    {
        $format = $this->find($id);

        if (null === $format)
        {
            throw new BookFormatNotFoundException();
        }

        return $format;
    }

//    /**
//     * @return BookFormat[] Returns an array of BookFormat objects
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

//    public function findOneBySomeField($value): ?BookFormat
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

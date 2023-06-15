<?php

namespace App\Repository;

use App\Entity\BookCategory;
use App\Models\IdResponse;
use App\src\Exceptions\CategoryNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookCategory>
 *
 * @method BookCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookCategory[]    findAll()
 * @method BookCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookCategory::class);
    }

    use RepositoryModifyTrait;

    /**
     * @return BookCategory[]
     */
    public function findAllSortedByAlphabet(): array
    {
        return $this->findBy([], ['title' => Criteria::ASC]);
    }

    public function existsById($id): bool
    {
        return null !== $this->find($id);
    }

    public function getById(int $id): BookCategory
    {
        $category = $this->find($id);

        if (null === $category)
        {
            throw new CategoryNotFoundException();
        }

        return $category;
    }

    /**
     * @return BookCategory[]
     */
    public function findBookCategoriesByIds(array $ids): array
    {
        return $this->findBy(['id' => $ids]);
    }

    public function existsBySlug(string $slug): bool
    {
        return null !== $this->findOneBy(["slug" => $slug]);
    }

    public function countBooksByCategory(int $categoryId): int
    {
        return $this->_em->createQuery('SELECT COUNT(b.id) FROM App\Entity\Book b WHERE :categoryId MEMBER OF b.categories')
            ->setParameter('categoryId', $categoryId)
            // выдает количество книг в категории
            ->getSingleScalarResult();
    }




}

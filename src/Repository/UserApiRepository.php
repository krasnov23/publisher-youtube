<?php

namespace App\Repository;

use App\Entity\UserApi;
use App\Exceptions\UserNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<UserApi>
 *
 * @method UserApi|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserApi|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserApi[]    findAll()
 * @method UserApi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserApiRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserApi::class);
    }

    public function existsByEmail(string $email): bool
    {
        return null !== $this->findOneBy(['email'=> $email]);
    }

    public function getUser(int $userId): UserApi
    {
        $user = $this->find($userId);

        if (null === $user)
        {
            throw new UserNotFoundException();
        }

        return $user;
    }

    use RepositoryModifyTrait;

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof UserApi) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}

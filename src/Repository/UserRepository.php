<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\Helper\PaginatorHelper;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }


    public function getUserByValidToken(string $token): ?User
    {
        $now = (new \DateTime())->modify('-1 days');
        return $this->createQueryBuilder('user')
                    ->where('user.resetPasswordToken = :token')
                    ->andWhere('user.resetPasswordRequestAt >= :now')
                    ->setParameters(['token' => $token, 'now' => $now])
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function getUsersByFiltersAndPaginator(array $filters = [], array $sort = null, int $limit = null, int $offset = null): array|Paginator
    {
        $queryBuilder = $this->createQueryBuilder('user');

        if (isset($filters['user']) && !empty($filters['user'])) {
            $queryBuilder->andWhere('user.id NOT IN (:user)')->setParameter('user', (array)$filters['user']);
        }

        $orX = $queryBuilder->expr()->orX();
        if (isset($filters['firstName']) && !empty($filters['firstName'])) {
            $orX->add('user.firstName LIKE :firstName');
            $queryBuilder->setParameter('firstName', '%' . $filters['firstName'] . '%');
        }

        if (isset($filters['lastName']) && !empty($filters['lastName'])) {
            $orX->add('user.lastName LIKE :lastName');
            $queryBuilder->setParameter('lastName', '%' . $filters['lastName'] . '%');
        }

        if (isset($filters['email']) && !empty($filters['email'])) {
            $orX->add('user.email LIKE :email');
            $queryBuilder->setParameter('email', '%' . $filters['email'] . '%');
        }

        if ($orX->count() !== 0) {
            $queryBuilder->andWhere($orX);
        }

        PaginatorHelper::applyPaginator($queryBuilder, $limit, $offset);
        PaginatorHelper::applyOrder($queryBuilder, $sort);

        return PaginatorHelper::results($queryBuilder);
    }
}

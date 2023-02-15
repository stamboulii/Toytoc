<?php

namespace App\Repository;

use App\Entity\Toy;
use App\Helper\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Toy>
 *
 * @method Toy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Toy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Toy[]    findAll()
 * @method Toy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ToyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Toy::class);
    }

    public function save(Toy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Toy $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getToyByFiltersAndPaginator(array $filters = [], array $sort = null, int $limit = null, int $offset = null): array|Paginator
    {
        $queryBuilder = $this->createQueryBuilder('toy');

        if (null !== $filters['user_id']) {
            $queryBuilder->andWhere('toy.user = :user')->setParameter('user', $filters['user_id']);
        }

        if (null !== $filters['category_id']) {
            $queryBuilder->andWhere('toy.User = :category')->setParameter('category', $filters['user_id']);
        }

        $orX = $queryBuilder->expr()->orX();
        if (isset($filters['state']) && !empty($filters['state'])) {
            $orX->add('toy.state LIKE :state');
            $queryBuilder->setParameter('state', '%' . $filters['state'] . '%');
        }

        if (isset($filters['price']) && !empty($filters['price'])) {
            $orX->add('toy.price LIKE :price');
            $queryBuilder->setParameter('price', '%' . $filters['price'] . '%');
        }
        if (isset($filters['weight']) && !empty($filters['weight'])) {
            $orX->add('toy.weight LIKE :weight');
            $queryBuilder->setParameter('weight', '%' . $filters['weight'] . '%');
        }


        if ($orX->count() !== 0) {
            $queryBuilder->andWhere($orX);
        }

        PaginatorHelper::applyPaginator($queryBuilder, $limit, $offset);
        PaginatorHelper::applyOrder($queryBuilder, $sort);

        return PaginatorHelper::results($queryBuilder);
    }
    public function findOneByIdJoinedToUser(int $toyId): ?Toy
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT t, u
            FROM App\Entity\Toy t
            INNER JOIN t.User u
            WHERE t.id = :id'
        )->setParameter('id', $toyId);

        return $query->getOneOrNullResult();
    }
//    /**
//     * @return Toy[] Returns an array of Toy objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Toy
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

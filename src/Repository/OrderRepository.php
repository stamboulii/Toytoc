<?php

namespace App\Repository;

use App\Entity\Toys_order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Toy;

/**
 * @extends ServiceEntityRepository<Toys_order>
 *
 * @method Toys_order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Toys_order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Toys_order[]    findAll()
 * @method Toys_order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Toys_order::class);
    }

    public function save(Toys_order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Toys_order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function isToyUnderBuying(Toy $toy): bool
    {
        return count($this->createQueryBuilder('e')
                          ->join('e.shipping', 's')
                          ->where('s.status = 0')
                          ->andWhere('e.toys like :toys')
                          ->setParameter('toys', '%: ' . $toy->getId() . '%')
                          ->getQuery()
                          ->getResult()) !== 0;
    }
}

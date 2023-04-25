<?php

namespace App\Repository;

use App\Entity\ContactUs;
use App\Helper\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactUs>
 *
 * @method ContactUs|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactUs|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactUs[]    findAll()
 * @method ContactUs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactUsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactUs::class);
    }

    public function save(ContactUs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactUs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getContactUsByFiltersAndPaginator(array $filters = [], array $sort = null, int $limit = null, int $offset = null): array|Paginator
    {
        $queryBuilder = $this->createQueryBuilder('contactus');


        $orX = $queryBuilder->expr()->orX();
        if (isset($filters['email']) && !empty($filters['email'])) {
            $orX->add('contactus.email LIKE :email');
            $queryBuilder->setParameter('email', '%' . $filters['email'] . '%');
        }
        if (isset($filters['name']) && !empty($filters['name'])) {
            $orX->add('contactus.name LIKE :name');
            $queryBuilder->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['subject']) && !empty($filters['subject'])) {
            $orX->add('category.subject LIKE :subject');
            $queryBuilder->setParameter('subject', '%' . $filters['subject'] . '%');
        }

        if (isset($filters['message']) && !empty($filters['message'])) {
            $orX->add('contactus.message LIKE :message');
            $queryBuilder->setParameter('message', '%' . $filters['message'] . '%');
        }


        if ($orX->count() !== 0) {
            $queryBuilder->andWhere($orX);
        }

        PaginatorHelper::applyPaginator($queryBuilder, $limit, $offset);
        PaginatorHelper::applyOrder($queryBuilder, $sort);

        return PaginatorHelper::results($queryBuilder);
    }


}

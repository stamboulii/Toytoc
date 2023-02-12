<?php

namespace App\Repository;

use App\Entity\Category;
use App\Helper\PaginatorHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getCategoryByFiltersAndPaginator(array $filters = [], array $sort = null, int $limit = null, int $offset = null): array|Paginator
    {
        $queryBuilder = $this->createQueryBuilder('category');


        $orX = $queryBuilder->expr()->orX();
        if (isset($filters['name']) && !empty($filters['name'])) {
            $orX->add('category.name LIKE :name');
            $queryBuilder->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['description']) && !empty($filters['description'])) {
            $orX->add('category.description LIKE :description');
            $queryBuilder->setParameter('description', '%' . $filters['description'] . '%');
        }


        if ($orX->count() !== 0) {
            $queryBuilder->andWhere($orX);
        }

        PaginatorHelper::applyPaginator($queryBuilder, $limit, $offset);
        PaginatorHelper::applyOrder($queryBuilder, $sort);

        return PaginatorHelper::results($queryBuilder);
    }



}

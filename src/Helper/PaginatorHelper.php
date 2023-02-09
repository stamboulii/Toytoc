<?php

namespace App\Helper;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorHelper
{
    public static function applyPaginator(QueryBuilder &$queryBuilder, int $limit = null, int $offset = null): void
    {
        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset) {
            $queryBuilder->setFirstResult($offset);
        }
    }

    public static function results(QueryBuilder &$queryBuilder): array|Paginator
    {
        return $queryBuilder->getMaxResults() ? new Paginator($queryBuilder->getQuery()) : $queryBuilder->getQuery()->execute();
    }

    public static function applyOrder(QueryBuilder &$queryBuilder, array $sort = null): void
    {
        if ($sort) {
            foreach ($sort as $field => $value) {
                $queryBuilder->addOrderBy($field, $value);
            }
        }
    }
}

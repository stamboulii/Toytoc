<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

class HttpQueryHelper
{
    public const DEFAULT_LIMIT = 10;
    public const DEFAULT_PAGE  = 1;
    public const DEFAULT_SORT  = 'asc';

    public static function getLimit(Request $request): int
    {
        return $request->query->getInt('limit', static::DEFAULT_LIMIT);
    }

    public static function getOffset(Request $request): int
    {
        return static::getLimit($request) * (static::getPage($request) - 1);
    }

    public static function getOrderBy(Request $request): ?array
    {
        return $request->query->has('order') && $request->query->has('sort') ?
            [$request->query->get('order') => $request->query->get('sort')] :
            null;
    }

    public static function getSort(Request $request): ?string
    {
        return $request->query->get('sort', static::DEFAULT_SORT);
    }

    public static function getOrder(Request $request): ?string
    {
        return $request->query->get('order');
    }

    public static function getPage(Request $request): ?string
    {
        return $request->query->get('page', static::DEFAULT_PAGE);
    }
}

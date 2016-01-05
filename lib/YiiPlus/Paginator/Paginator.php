<?php

namespace YiiPlus\Paginator;

use yii\db\ActiveQueryInterface;

/**
 * 分页工厂类
 */
class Paginator
{
    public function paginate($data, $currentPage = 1, $pageSize = 10)
    {
        if ($data instanceof ActiveQueryInterface) {
            return Pagination\ActiveQueryPagination::paginate($data, $currentPage, $pageSize);
        } else {
            throw new \InvalidArgumentException();
        }
    }
}

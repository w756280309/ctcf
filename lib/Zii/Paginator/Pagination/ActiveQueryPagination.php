<?php

namespace Zii\Paginator\Pagination;

use yii\data\Pagination;
use yii\db\ActiveQueryInterface;

class ActiveQueryPagination extends Pagination implements \JsonSerializable
{
    private $_query;
    private $_items;

    public static function paginate(ActiveQueryInterface $query, $currentPage = 1, $pageSize = 10)
    {
        // 从查询Query上clone一个用于计数
        $countQuery = clone $query;

        // 构造一个Pagination实例
        $pg = new self([
            'totalCount' => $countQuery->count(),
            'pageSize' => $pageSize,
        ]);
        $pg->setQuery($query);
        $pg->setPage($currentPage - 1); // 设置当前页码

        return $pg;
    }

    public function setQuery(ActiveQueryInterface $query)
    {
        $this->_query = $query;
    }

    public function getItems()
    {
        if (null === $this->_items) {
            $this->_items = $this->_query->offset($this->getOffset())
                ->limit($this->getLimit())
                ->asArray()
                ->all();
        }

        return $this->_items;
    }

    public function jsonSerialize()
    {
        return [
            'count' => $this->totalCount,
            'size' => $this->getPageSize(),
            'tp' => $this->getPageCount(),
            'cp' => $this->getPage() + 1,
        ];
    }
}

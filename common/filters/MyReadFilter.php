<?php

namespace common\filters;

use PHPExcel_Reader_IReadFilter;

class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
    const MAX_READ_LINE = 1000;
    public $columns;

    public function __construct($columns)
    {
        $this->columns = !empty($columns) ? $columns : range('A', 'Z');
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Read rows 1 to 1000 and columns A to F only
        if ($row >= 1 && $row <= static::MAX_READ_LINE) {
            if (in_array($column, $this->columns)) {
                return true;
            }
        }
        return false;
    }
}
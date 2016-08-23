<?php

namespace common\filters;

use PHPExcel_Reader_IReadFilter;

class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName = '') {
        //  Read rows 1 to 1000 and columns A to F only
        if ($row >= 1 && $row <= 1000) {
            if (in_array($column, range('A', 'F'))) {
                return true;
            }
        }
        return false;
    }
}
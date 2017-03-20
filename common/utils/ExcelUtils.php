<?php

namespace common\utils;


class ExcelUtils
{
    /**
     * @param string $filePath 文件路径
     * @param string $maxCol 最大列号, 默认Z
     * @param int    $maxRow 最大行号, 默认1000
     * @param string $minCol 最小列号, 默认A
     * @param int    $minRow 最小行号, 默认1
     * @return array|null
     *
     * DEMO:获取上传文件(fileInput 的 name 是 'file')的A1-D1000范围内的数据(实际行数小于等于最大行数)
     * $upload = \yii\web\UploadedFile;::getInstanceByName('file');
     * if (!$upload->getHasError()) {
     *      $data = \common\utils\ExcelUtils::readExcelToArray($upload->tempName, 'D')
     * }
     *
     */
    public static function readExcelToArray($filePath, $maxCol = 'Z', $maxRow = 1000, $minCol = 'A', $minRow = 1)
    {
        if (file_exists($filePath)) {
            $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
            $maxRow = intval(min($objPHPExcel->getActiveSheet()->getHighestRow(), $maxRow));
            return $objPHPExcel->getActiveSheet()->rangeToArray("$minCol$minRow:$maxCol$maxRow");
        }
        return null;
    }
}
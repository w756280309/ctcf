<?php

namespace console\command;


use common\lib\user\UserStats;
use common\models\queue\Job;
use Yii;

class SqlExportJob extends Job
{
    public function run()
    {
        $sql = $this->getParam('sql');
        $queryParams = $this->getParam('queryParams');
        $exportSn = $this->getParam('exportSn');
        $itemLabels = $this->getParam('itemLabels');
        $itemType = $this->getParam('itemType');
        $labelLength = count($itemLabels);
        if (count($itemType) !== $labelLength) {
            $itemType = null;
        }
        $itemType = array_values($itemType);

        $command = Yii::$app->db->createCommand($sql);
        if (!empty($queryParams)) {
            $command = $command->bindValues($queryParams);
        }
        $data = $command->queryAll();

        $exportData[] = $itemLabels;
        foreach ($data as $num => $item) {
            $item = array_values($item);
            if (count($item) !== $labelLength) {
                throw new \Exception('sql查询数据项和标题项个数不同');
            }
            if (!is_null($itemType)) {
                foreach ($item as $key => $value) {
                    if (isset($itemType[$key])) {
                        switch ($itemType[$key]) {
                            case 'int':
                            case 'integer':
                                $item[$key] = intval($value);break;
                            case 'float':
                                $item[$key] = floatval($value);break;
                            case 'date':
                            case 'dateTime':
                            case 'string':
                            default:
                                $item[$key] = strval($value);
                        }
                    }
                }
            }
            $data[$num] = $item;
        }
        if (!empty($data)) {
            $exportData = array_merge($exportData, $data);
        }
        $path = rtrim(\Yii::$app->params['backend_tmp_share_path'], '/');
        $file = $path . '/' . $exportSn . '.xlsx';//todo 暂时不做下载sn和对应文件名的关联

        if (!file_exists($file)) {
            $objPHPExcel = UserStats::initPhpExcelObject($exportData);
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($file);
            exit(0);
        }
        exit(1);
    }
}
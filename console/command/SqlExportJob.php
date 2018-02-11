<?php

namespace console\command;


use common\lib\user\UserStats;
use common\models\queue\Job;
use Yii;
use common\utils\SecurityUtils;

class SqlExportJob extends Job
{
    public function run()
    {
        $sql = $this->getParam('sql');
        $queryParams = $this->getParam('queryParams');
        $exportSn = $this->getParam('exportSn');
        $itemLabels = $this->getParam('itemLabels');
        $itemType = $this->getParam('itemType');
        $paramKey = $this->getParam('key');
        $labelLength = count($itemLabels);
        if (count($itemType) !== $labelLength) {
            $itemType = null;
        }
        $itemType = array_values($itemType);

        if (('export_referral_user_info' === $paramKey || 'export_referral_user_count' === $paramKey) && !empty($queryParams['campaignSource'])) {
            $campaignSource = trim($queryParams['campaignSource'], ',');
            unset($queryParams['campaignSource']);
            $campaignArr = explode(',', $campaignSource);
            $len = count($campaignArr);
            $paramKeysIn = [];
            $pdoKeys = '';
            for ($i = 0; $i < $len; $i++) {
                $keyV = 'v' . $i;
                $pdoKeys = $pdoKeys . ':v' . $i . ',';
                $paramKeysIn[] = $keyV;
            }
            $pdoKeys = rtrim($pdoKeys, ',');
            $sql = str_replace(":campaignSource", $pdoKeys, $sql);
            $campaignSources = array_combine($paramKeysIn, $campaignArr);
            $queryParams = array_merge($queryParams, $campaignSources);
        }
        $command = Yii::$app->db->createCommand($sql);
        if (!empty($queryParams)) {
            $command = $command->bindValues($queryParams);
        }
        $data = $command->queryAll();
        $exportData[] = $itemLabels;
        foreach ($data as $num => $item) {
            if (isset($item['手机号'])) {
                $item['手机号'] = SecurityUtils::decrypt($item['手机号']);
            }
            if ('repayment_expire_interest' === $paramKey) {
                $item['年龄'] = date('Y') - substr(SecurityUtils::decrypt($item['年龄']), 6, 4);
                $item['原计划还款时间'] = date('Y-m-d', $item['原计划还款时间']);
            } else if ('last_ten_day_draw' === $paramKey) {
                $item['未投资时长'] = (new \DateTime)->diff(new \DateTime($item['未投资时长']))->days;
            } else if ('order_no_licai_plan' === $paramKey) {
                $item['身份证号'] = SecurityUtils::decrypt($item['身份证号']);
            }  else if ('xs_due_list_export' === $paramKey) {
                $item['分销商'] = is_null($item['分销商']) ? '官方' : $item['分销商'];
            } else if ('export_nbxdjb_finish' === $paramKey) {
                $item['联系方式'] = SecurityUtils::decrypt($item['联系方式']);
                $item['单位'] = $item['单位'] > 1 ? '月' : '天';
                $item['到期日'] = date('Y-m-d', $item['到期日']);
            }

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

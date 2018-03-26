<?php

namespace console\command;


use common\lib\user\UserStats;
use common\models\order\OnlineRepaymentPlan;
use common\models\product\OnlineProduct;
use common\models\queue\Job;
use common\models\tx\UserAsset;
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
        if ($paramKey == 'repayment_expire_interest') { //指定日期还款数据
            $data = $this->getRepaymentExpireInterest($queryParams['repaymentDate']);
        } else {
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
        }
        $exportData[] = $itemLabels;
        foreach ($data as $num => $item) {
            if (isset($item['手机号'])) {
                $item['手机号'] = SecurityUtils::decrypt($item['手机号']);
            }
            if ('repayment_expire_interest' === $paramKey) {
//                $item['年龄'] = date('Y') - substr(SecurityUtils::decrypt($item['年龄']), 6, 4);
//                $item['原计划还款时间'] = date('Y-m-d', $item['原计划还款时间']);
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

    /**
     * 获取指定日期还款数据
     * @param $date
     */
    public function getRepaymentExpireInterest($date)
    {
        $models = OnlineRepaymentPlan::find()
            ->innerJoin('online_product', 'online_product.id = online_repayment_plan.online_pid')
            ->where([
                'online_product.status' => ['5', '6'],
                'online_product.isTest' => false,
                'online_repayment_plan.status' => ['1', '2'],
                'date(`online_repayment_plan`.`actualRefundTime`)' => $date,
            ])->all();
        $data = [];
        foreach ($models as $model) {
            $user = $model->user;
            $order = $model->order;
            $loan = $model->loan;
            if (!is_null($user) && !is_null($order) && !is_null($loan)) {
                $userAsset = UserAsset::findOne([
                    'user_id' => $user->id,
                    'loan_id' => $loan->id,
                    'order_id' => $order->id,
                ]);
                if (!is_null($userAsset) && $userAsset->amount > 0) {
                    array_push($data, [
                        $user->real_name,   //姓名
                        $user->getMobile(), //手机号
                        $user->crmAge,      //年龄
                        $user->userAffiliation ? $user->userAffiliation->affiliator->name : '官方', //分销商
                        bcdiv($userAsset->amount, 100),  //投资金额
                        $order->yield_rate, //利率
                        !empty($userAsset->credit_order_id) ? '[转让]'.$loan->title : $loan->title,   //标的名称
                        Yii::$app->params['refund_method'][$loan->refund_method],   //还款方式
                        $loan->status == OnlineProduct::STATUS_HUAN  ? '还款中' : '已还清',  //标的状态
                        date('Y-m-d', $loan->finish_date),     //标的截止日期
                        $model->benjin, //还款本金
                        $model->lixi,  //还款利息
                        $model->benxi,  //还款本息
                        date('Y-m-d', strtotime($model->actualRefundTime)),   //实际还款时间
                        date('Y-m-d', $loan->finish_date), //原计划还款时间
                        $user->lendAccount->available_balance,  //可用余额
                    ]);
                }
            }
        }
        return $data;
    }
}

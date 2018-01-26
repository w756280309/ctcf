<?php

namespace console\controllers;

use common\lib\user\UserStats;
use common\models\offline\OfflineLoan;
use common\models\offline\OfflineRepaymentPlan;
use common\models\offline\OfflineUser;
use common\utils\ExcelUtils;
use Wcg\Math\Bc;
use Wcg\Xii\Crm\Model\OfflineOrder;
use yii\console\Controller;

class OfflineController extends Controller
{
    /**
     * 增加历史标的SN
     */
    public function actionAddsn()
    {
        $offloans = OfflineLoan::find()
            ->where(['sn' => null])
            ->orWhere(['sn' => ''])
            ->all();
        $num = 0;
        if (null !== $offloans) {
            foreach ($offloans as  $offloan) {
                $offloan->sn = uniqid('OF2017');
                $offloan->save(false);
                $num++;
            }
        }
        echo "OfflineLoan共修改了" . $num ."条记录";
    }

    //更新客户银行卡信息
    public function actionUpdateOrder($action = null)
    {
        $filePath = \Yii::getAlias('@backend') . '/web/upload/offline/1219.xls';
        $arrs = ExcelUtils::readExcelToArray($filePath, 'H', 10000);
        $total = count($arrs) - 1;  //一共的数量
        $successNum = 0;    //匹配成功的数量
        $arrError = []; //错误的记录
        $arrError[] = ['产品名称', '客户姓名', '证件号', '联系电话', '开户行名称', '银行卡账号', '认购金额（万）', '认购日', '失败原因'];
        foreach ($arrs as $k => $arr) {
            if ($k == 0) {
                continue;
            }
            $err_message = null;
            //判断标的
            $loan = OfflineLoan::findOne(['title' => $arr[0]]);
            if (!is_null($loan)) {
                //判断用户
                $user = OfflineUser::findOne(['realName' => $arr[1], 'idCard' => $arr[2]]);
                if (!is_null($user)) {
                    /**
                     * 判断订单
                     * 不能为多个
                     */
                    $orders = OfflineOrder::find()->where([
                        'loan_id' => $loan->id,     //产品id
                        'mobile' => $arr[3],        //手机号
                        'idCard' => $arr[2],        //证件号
                        'money' => $arr[6],         //投资金额
                        'orderDate' => date('Y-m-d', strtotime(str_replace('/', '-', $arr[7]))),  //认购日期
                        'isDeleted' => false,
                    ])->all();
                    if (count($orders) == 1) {
                        $successNum += 1;
                        $order = $orders[0];
                        if ($action) {
                            $order->accBankName = $arr[4];   //开户行
                            $order->bankCardNo = $arr[5];    //卡号
                            if (!$order->save(false)) {
                                $err_message = '订单【'.$order->id.'】更新失败';
                            }
                        }
                    } else {
                        $err_message = '线下订单不存在或存在多个同样的订单';
                    }
                } else {
                    $err_message = '线下用户不存在';
                }

            } else {
                $err_message =  '线下标的不存在';
            }

            if ($err_message) {
                array_push($arr, $err_message);
                $arrError[] = $arr;
            }
        }
        //生成更新失败的文件
        if (count($arrError) > 1 && $action) {
            $objPHPExcel = UserStats::initPhpExcelObject($arrError);
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $file = \Yii::getAlias('@backend') . '/web/upload/offline/线下导入失败'.time().'.xls';
            $objWriter->save($file);
        }
        if (!$action) {
            $this->stdout('总记录：'.$total.'条。'.PHP_EOL.'匹配成功数量：'.$successNum.'条。'.PHP_EOL.'匹配失败数量：'.bcsub($total, $successNum).'条。' . PHP_EOL);
        }
    }

    /**
     * 2018-01-18
     * 用于修复【宁富2号三都国资第六期】的2018-06-20  期和 2018-09-18
     * 的还款计划
     */
    public function actionUpdateOfflineRepayment($loanId = 67, $action = false)
    {
        //取出所有需要修改的还款计划
        $models = OfflineRepaymentPlan::find()
            ->where([
                'loan_id' => $loanId,
                'qishu' => ['4', '5'],
            ])
            ->andWhere(['not in', 'uid', ['484']])  //郑金华除外
            ->all();
        if (!$action) {
            $this->stdout('共有数据：'.count($models).'条需要修复。');
            die;
        }
        $num = 0;
        foreach ($models as $model) {
            //计算利息
            $days = $model->qishu == 4 ? 183 : 89;
            //本金*利率*天数/365
            $model->lixi = Bc::round(bcdiv(bcmul(bcmul(bcmul($model->order->money, 10000, 14), $model->order->apr, 14), $days, 14), 365, 14), 2);
            $model->benxi = Bc::round(bcadd($model->benjin, $model->lixi, 14), 2);
            if ($model->save(false)) {
                $num ++;
            }
        }
        $this->stdout('共修复数据：'.$num.'条。');
    }
}

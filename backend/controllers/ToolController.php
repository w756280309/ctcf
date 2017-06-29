<?php

namespace backend\controllers;

use common\models\user\RechargeRecord;
use Yii;

class ToolController extends BaseController
{
    public function actionIndex()
    {
        $this->layout = 'frame';
        $platformUserId = Yii::$app->params['ump']['merchant_id'];//平台在联动账户
        $ump = Yii::$container->get('ump');
        //平台信息
        $ret = $ump->getMerchantInfo($platformUserId);

        return $this->render('index', [
            'platformBalance' => bcdiv($ret->get('balance'), 100, 2),
        ]);
    }

    /**
     * 平台资金账户充值工具
     *
     * @param $money
     */
    public function actionRecharge($money)
    {
        if ($money <= 0) {
            die('充值金额不合法');
        }
        $money = bcadd($money, 0, 2);
        $admin = $this->getAuthedUser();
        $file = Yii::getAlias('@app/runtime/logs/org_recharge.txt');
        file_put_contents($file, date('Y-m-d H:i:s') . "| 后台用户 {$admin->id} 正在为平台账号充值，充值金额 {$money} 元 \n", FILE_APPEND);
        $platformUserId = Yii::$app->params['ump']['merchant_id'];//平台在联动账户
        $ump = Yii::$container->get('ump');
        //平台信息
        $ret = $ump->getMerchantInfo($platformUserId);
        file_put_contents($file, date('Y-m-d H:i:s') . "| 当前平台在联动账户余额 {$ret->get('balance')} 分 \n", FILE_APPEND);

        $recharge = new RechargeRecord([
            'sn' => RechargeRecord::createSN(),
            'created_at' => time(),
            'fund' => $money,
        ]);
        file_put_contents($file, date('Y-m-d H:i:s') . "|  正在请求联动进行充值，充值订单sn {$recharge->sn} \n\n", FILE_APPEND);
        $ump->OrgRechargeApply($recharge, 'B2BBANK', $platformUserId, 'BOC');
    }

}
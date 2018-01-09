<?php

namespace backend\controllers;

use common\models\user\RechargeRecord;
use Yii;

class LhwtController extends BaseController
{
    //立合旺通充值初始页
    public function actionIndex()
    {
        $this->layout = 'frame';
        $platformUserId = Yii::$app->params['ump']['lhwt_merchant_id'];//平台在联动账户
        $ump = Yii::$container->get('ump');
        //平台信息
        $ret = $ump->getMerchantInfo($platformUserId);

        return $this->render('lhwt', [
            'platformBalance' => bcdiv($ret->get('balance'), 100, 2),
        ]);
    }

    //立合旺通企业投资者账户充值
    public function actionRecharge($money)
    {
        if ($money <= 0) {
            die('充值金额不合法');
        }
        $money = bcadd($money, 0, 2);
        $file = Yii::getAlias('@app/runtime/logs/lhwt_recharge.txt');
        $admin = $this->getAuthedUser();
        file_put_contents($file, date('Y-m-d H:i:s') . "| 后台用户 {$admin->id} 正在为联动企业投资者账户账号充值，充值金额 {$money} 元 \n", FILE_APPEND);
        $platformUserId = Yii::$app->params['ump']['lhwt_merchant_id'];//立合旺通在联动投资者账户
        $ump = Yii::$container->get('ump');
//平台信息
        $ret = $ump->getMerchantInfo($platformUserId);
        file_put_contents($file, date('Y-m-d H:i:s') . "| 当前联动企业投资者账户在联动账户余额 {$ret->get('balance')} 分 \n", FILE_APPEND);

        $recharge = new RechargeRecord([
            'sn' => RechargeRecord::createSN(),
            'created_at' => time(),
            'fund' => $money,
        ]);
        file_put_contents($file, date('Y-m-d H:i:s') . "|  正在请求联动进行充值，充值金额：{$recharge->fund}，充值时间：{$recharge->created_at}充值订单sn:{$recharge->sn} \n\n", FILE_APPEND);
        $ump->OrgRechargeApply($recharge, 'B2BBANK', $platformUserId, 'CMB');
    }
}

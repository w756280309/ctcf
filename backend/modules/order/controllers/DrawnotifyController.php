<?php

namespace backend\modules\order\controllers;

use Yii;
use yii\web\Controller;
use common\models\draw\DrawManager;
use common\models\user\DrawRecord;
use common\models\TradeLog;
use common\models\order\OnlineFangkuan;

class DrawnotifyController extends Controller
{
    /**
     * 融资会员提现后台通知
     */
    public function actionNotify()
    {
        $data = Yii::$app->request->get();
        TradeLog::initLog(2, $data, $data['sign'])->save();
        $ump = Yii::$container->get('ump');
        $err = '0000';
        $errMsg = 'No err';

        if ($ump->verifySign($data)
            && '0000' === $data['ret_code']
            && '4' === $data['trade_state']
        ) {
            $draw = DrawRecord::findOne(['sn' => $data['order_id']]);
            if (!$draw) {
                $err = '9999';
                $errMsg = '找不到对应的提现记录';
            }

            DrawManager::commitDraw($draw);//确定提现完成 最终态

            $onlineFangkuan = OnlineFangkuan::findOne(['sn' => $draw->orderSn]);
            if (!$onlineFangkuan) {
                $err = '9999';
                $errMsg = '找不到对应的放款记录';
            } else {
                $onlineFangkuan->status = OnlineFangkuan::STATUS_TIXIAN_SUCC;
                if (!$onlineFangkuan->save()) {
                    $err = '9999';
                    $errMsg = '数据库错误';
                }
            }
        } else {
            $err = '9999';
        }

        $content = $ump->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        $this->layout = false;
        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }
}

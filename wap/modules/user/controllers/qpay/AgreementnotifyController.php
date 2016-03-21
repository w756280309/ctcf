<?php

namespace app\modules\user\controllers\qpay;

use Yii;
use yii\web\Controller;
use common\models\TradeLog;

/**
 * 免密支付回调地址
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class AgreementnotifyController extends Controller
{
    /**
     * 前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        $this->processing($data);
    }

    /**
     * 后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = "no error";
        $data = Yii::$app->request->get();
        $this->processing($data);
    }

    public static function processing($data)
    {
        TradeLog::initLog(2, $data, $data['sign'])->save();
        var_dump($data);
    }
}

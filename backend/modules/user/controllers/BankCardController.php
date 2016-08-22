<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use common\models\bank\BankCardUpdate;
use common\models\user\QpayBinding;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class BankCardController extends BaseController
{
    /**
     * 银行卡号列表.
     */
    public function actionList($uid)
    {
        $qpay = QpayBinding::find()->where(['uid' => $uid])->all();
        $update = BankCardUpdate::find()->where(['uid' => $uid])->all();
        $data = ArrayHelper::merge($qpay, $update);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);

        return $this->render('list', ['dataProvider' => $dataProvider, 'uid' => $uid]);
    }

    /**
     * 查询用户银行卡信息在联动一侧的状态.
     */
    public function actionUmpInfo($id, $type)
    {
        if ('b' === $type) {
            $model = QpayBinding::findOne($id);
        } elseif ('u' === $type) {
            $model = BankCardUpdate::findOne($id);
        } else {
            return ['code' => 1, 'message' => '参数错误'];
        }

        $resp = Yii::$container->get('ump')->getBindingTx($model);

        if ($resp->isSuccessful()) {
            $mess = '商户订单编号：'.$resp->get('order_id').'<br>'
                .'商户订单日期：'.$resp->get('mer_date').'<br>'
                .'返回码：'.$resp->get('ret_code').'<br>'
                .'返回信息：'.$resp->get('ret_msg');

            return ['code' => 0, 'message' => $mess];
        } else {
            $mess = '返回码：'.$resp->get('ret_code').'<br>'
                .'返回信息：'.$resp->get('ret_msg');

            return ['code' => 1, 'message' => $mess];
        }
    }
}

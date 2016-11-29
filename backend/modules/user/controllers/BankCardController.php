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
        ArrayHelper::multisort($data, 'created_at', SORT_DESC);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->renderFile('@backend/modules/user/views/bank-card/list.php', ['dataProvider' => $dataProvider, 'uid' => $uid]);
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
            $tranState = (int) $resp->get('tran_state');
            if (1 === $tranState) {
                $result = '处理中（涉及资料上传，人工审核）';
            } elseif (2 === $tranState) {
                $result = '成功';
            } elseif (3 === $tranState) {
                $result = '失败';
            } elseif (4 === $tranState) {
                $result = '不明（需要人工查询）';
            } elseif (6 === $tranState) {
                $result = '其他（需要人工查询）';
            } else {
                $result = $tranState;
            }

            $mess = '商户订单编号：'.$resp->get('order_id').'<br>'
                .'商户订单日期：'.$resp->get('mer_date').'<br>'
                .'返回码：'.$resp->get('ret_code').'<br>'
                .'返回信息：'.$resp->get('ret_msg').'<br>'
                .'返回结果：'.$result;

            return ['code' => 0, 'message' => $mess];
        } else {
            $mess = '返回码：'.$resp->get('ret_code').'<br>'
                .'返回信息：'.$resp->get('ret_msg');

            return ['code' => 1, 'message' => $mess];
        }
    }
}

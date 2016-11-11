<?php

namespace console\controllers;

use common\models\product\OnlineProduct;
use Yii;
use yii\console\Controller;

/**
 * 系统监测
 */
class EnvironmentController extends Controller
{
    //监测联动环境
    public function actionUmp()
    {
        $ump = Yii::$container->get('ump');
        $loan = OnlineProduct::find()->where(['del_status' => 0])->orderBy(['id' => SORT_DESC])->one();
        try {
            if (!empty($loan)) {
                $res = $ump->getLoanInfo($loan->id);
                if ($res->isSuccessful()) {
                    echo 0;
                }
            }
        } catch (\Exception $ex) {
            echo -1;
        }
    }

    //监测交易系统
    public function actionTx()
    {
        try {
            $respData = Yii::$container->get('txClient')->get('credit-note/list');
            if (!empty($respData)) {
                echo 0;
            }
        } catch (\Exception $ex) {
            echo -1;
        }
    }
}
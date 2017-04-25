<?php

namespace console\controllers;

use common\models\offline\OfflineLoan;
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
}

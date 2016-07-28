<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\couponcode\CouponCode;

class CouponcodeController extends Controller
{
    public function actionInsertrecords()
    {
        $db = Yii::$app->db;
        $initnum = CouponCode::find()->count();
        if ($initnum >= 50000) {
            exit;
        }
        $expiresAt = '2016-09-28 23:59:59';
        $createdAt = date('Y-m-d H:i:s');
        for ($j = 0; $j < 100; $j++) {
            $brr = array();
            for ($i = 0; $i < 500; $i++) {
                array_push($brr, [$this->createRandomStr(), $expiresAt, $createdAt]);
            }
            $db->createCommand()->batchInsert('coupon_code', ['code', 'expiresAt', 'createdAt'], $brr)->execute();
        }
        $finalnum = CouponCode::find()->count();
        $insertnum = $finalnum - $initnum;
        echo "当前表里共有{$finalnum}条,本次总共插入{$insertnum}条兑换码记录";

    }

    private function createRandomStr()
    {
        $code = '';
        $str = "abcdefghjkmnpqrstuvwABCDEFGHJKMNPQRSTUVWXYZ012345789";
        $length = strlen($str) - 1;
        for ( $i = 0; $i < 16; $i++ ) {
            $code .= substr($str, mt_rand(0, $length), 1);
        }
        return $code;
    }
}

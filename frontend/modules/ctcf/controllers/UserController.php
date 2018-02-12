<?php

namespace frontend\modules\ctcf\controllers;

use common\service\BankService;
use yii\web\Controller;

class UserController extends Controller
{
    //老用户实名认证页面自动填充姓名和身份证号
    public function actionGetNameAndCard()
    {
        if (!\Yii::$app->user->isGuest) {
            $userId = \Yii::$app->user->id;
            $userInfo = \Yii::$app->db->createCommand("select real_name,idCard from user_old where userId = :userId", [
                ':userId' => $userId
            ])->queryOne();
            return $userInfo;
        }
    }
    //绑定银行卡页面自动填充卡号,如果是招商银行，则返回null
    public function actionGetCardNumber()
    {
        if (!\Yii::$app->user->isGuest) {
            $userId = \Yii::$app->user->id;
            $userInfo = \Yii::$app->db->createCommand("select cardNumber from user_old where userId = :userId", [
                ':userId' => $userId
            ])->queryOne();
            if ($userInfo) {
                $bankInfo = BankService::checkBankcard($userInfo['cardNumber']);
                if ($bankInfo['bank_name'] == '招商银行') {
                    return null;
                }
                return $userInfo;
            }
        }
    }
}

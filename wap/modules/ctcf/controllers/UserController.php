<?php

namespace wap\modules\ctcf\controllers;

use common\models\user\User;
use common\utils\SecurityUtils;
use yii\web\Controller;

class UserController extends Controller
{
    //
    public function actionGetNameAndCard()
    {
        $userId = \Yii::$app->user->id;
        $userInfo = \Yii::$app->db->createCommand("select real_name,safeIdCard from user_old where userId=".$userId)->queryOne();
        return $userInfo;
    }
}

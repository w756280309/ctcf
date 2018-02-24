<?php

namespace common\ctcf\controllers;

use yii\web\Controller;
use Yii;

class PopController extends Controller
{
    /**
     * 首页弹窗类型.
     */
    public function actionPopType()
    {
        //判断是否登录
        if (Yii::$app->user->isGuest) {
            $type = 1;
        } else {
            $user = Yii::$app->user->getIdentity();
            $redis = Yii::$app->redis;
            if ($redis->exists('oldUserRewardPop_' . $user->id)) {
                $type = $redis->get('oldUserRewardPop_' . $user->id);
            } else {
                $type = 0;
            }
        }

        return $type;
    }

    /**
     * 弹窗后处理.
     */
    public function actionAfterPop()
    {
        //判断是否登录
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->getIdentity();
            $redis = Yii::$app->redis;
            if ($redis->exists('oldUserRewardPop_' . $user->id)) {
                $redis->del('oldUserRewardPop_' . $user->id);
            }
        }
    }
}

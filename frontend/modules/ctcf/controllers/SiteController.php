<?php

namespace frontend\modules\ctcf\controllers;

use frontend\controllers\BaseController;
use Yii;

class SiteController extends BaseController
{
    /**
     * 首页弹窗类型.
     */
    public function actionPopType()
    {
        //判断是否登录
        $user = Yii::$app->user->getIdentity();
        if (is_null($user)) {
            $type = 1;
        } else {
            $redis = Yii::$app->redis_session;
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
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $redis = Yii::$app->redis_session;
            if ($redis->exists('oldUserRewardPop_' . $user->id)) {
                $redis->del('oldUserRewardPop_' . $user->id);
            }
        }
    }
}

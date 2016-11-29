<?php

namespace wap\modules\promotion\controllers;

use common\models\adv\Share;
use yii\web\Controller;

class P1611Controller extends Controller
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 百万年终奖页面.
     */
    public function actionOneMillion($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('one_million', ['share' => $share]);
    }

    /**
     * 新手活动页.
     */
    public function actionNewUser($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('new_user', ['share' => $share]);
    }
}
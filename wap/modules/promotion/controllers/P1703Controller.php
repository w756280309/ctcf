<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use yii\web\Controller;

class P1703Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 植树节活动.
     */
    public function actionTree($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('tree', [
            'share' => $share,
        ]);
    }

    /**
     * 315活动.
     */
    public function actionP315($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('p315', [
            'share' => $share,
        ]);
    }

    /**
     * 清明活动.
     */
    public function actionQm($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('qingming', [
            'share' => $share,
        ]);
    }
}
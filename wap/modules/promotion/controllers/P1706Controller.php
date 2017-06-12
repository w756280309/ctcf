<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use Yii;
use yii\web\Controller;

class P1706Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 微博活动.
     */
    public function actionWeibo($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('weibo', [
            'share' => $share,
        ]);
    }

    /**
     * 父亲节活动.
     */
    public function actionFathersDay($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('fathers_day', [
            'share' => $share,
        ]);
    }
}

<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use yii\web\Controller;

class P1704Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 世界卫生日活动.
     */
    public function actionWorldHealthDay($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('world_health_day', [
            'share' => $share,
        ]);
    }

    /**
     * 双倍积分活动.
     */
    public function actionDoublePoints($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('double_points', [
            'share' => $share,
        ]);
    }
}
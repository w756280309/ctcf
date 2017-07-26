<?php

namespace wap\modules\promotion\controllers;

use common\models\adv\Share;
use yii\web\Controller;

class P1707Controller extends Controller
{
    public $layout = '@app/views/layouts/fe';

    public function actionMonthEnd($wx_share_key = null)
    {
        $share = null;
        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('index', [
            'share' => $share,
        ]);
    }
}

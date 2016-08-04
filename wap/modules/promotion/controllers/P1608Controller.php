<?php

namespace wap\modules\promotion\controllers;

use yii\web\Controller;

class P1608Controller extends Controller
{
    /**
     * 邀请好友.
     */
    public function actionInvite()
    {
        $this->layout = false;
        return $this->render('invite');
    }
}

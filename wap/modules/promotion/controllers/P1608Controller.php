<?php

namespace wap\modules\promotion\controllers;

use app\controllers\BaseController;

class P1608Controller extends BaseController
{
    /**
     * 邀请好友.
     */
    public function actionInvite()
    {
        $this->layout = false;

        return $this->render('invite', ['user' => $this->getAuthedUser()]);
    }
}

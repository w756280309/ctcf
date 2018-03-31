<?php

namespace wap\modules\promotion\controllers;


class P180330Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 慈善早餐活动
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}

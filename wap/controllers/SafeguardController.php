<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use yii\web\Controller;

/**
 * 安全保障类.
 */
class SafeguardController extends Controller
{
    use HelpersTrait;
    /**
     * 安全保障列表页.
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 安全保障数据安全页.
     */
    public function actionDataRegard()
    {
        return $this->render('data-regard');
    }

    /**
     * 安全保障信息安全页.
     */
    public function actionInformationRegard()
    {
        return $this->render('information-regard');
    }

    /**
     * 安全保障专业合规页
     */
    public function actionMajorRegard()
    {
        return $this->render('major-regard');
    }

    /**
     * 安全保障风控先进页
     */
    public function actionRiskRegard()
    {
        return $this->render('risk-regard');
    }
}

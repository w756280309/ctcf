<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use wap\modules\promotion\models\RankingPromo;
use yii\web\Controller;

class NormalController extends Controller
{
    use HelpersTrait;
    public $layout = '@app/views/layouts/fe';

    public function actionMallGuide()
    {
        $promo = RankingPromo::find()
            ->where(['key' => 'mall_guide'])
            ->one();
        $config = json_decode($promo->config, true);
        if (null === $promo || empty($config)) {
            throw $this->ex404();
        }

        return $this->render('points_guide', [
            'detail' => $config,
        ]);
    }

    public function actionMall77()
    {
        return $this->render('points77');
    }

    public function actionMall170907()
    {
        return $this->render('170907');
    }

    public function actionMall1111()
    {
        return $this->render('points171111');
    }
}

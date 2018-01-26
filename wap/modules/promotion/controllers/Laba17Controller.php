<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\View;

/**
 * 腊八活动
 * Class Laba17Controller
 * @package wap\modules\promotion\controllers
 */
class Laba17Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'laba17']);
        $responseData = json_encode([
            'isLoggedIn' => null !== $this->getAuthedUser(),
            'promoStatus' => $this->getPromoStatus($promo),
        ]);
        $view = Yii::$app->view;
        $js = <<<JS
var dataStr = '$responseData';
var datas = eval('(' + dataStr + ')');
JS;
        $view->registerJs($js, View::POS_HEAD);

        return $this->render('index');
    }
}
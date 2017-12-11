<?php

namespace wap\modules\promotion\controllers;

use yii\web\View;

class P171220Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 初始化页面
     */
    public function actionIndex()
    {
        $isGuest = \Yii::$app->user->isGuest;
        $data = json_encode([
            'restTime' => strtotime('2017-12-20 10:00:00') - time(),
            'isLoggedIn' => !$isGuest,
            'promoStatus' => 1,
            'data' => [
                [
                    'name' => '格力取暖器',
                    'sn' => '17122010',
                    'currentPoints' => 10,
                    'allNum' => $isGuest ? 0 : 1,
                    'alreadyNum' => $isGuest ? 0 : 1,
                ],
                [
                    'name' => '飞利浦面包机',
                    'sn' => '17122016',
                    'currentPoints' => 10,
                    'allNum' => $isGuest ? 0 : 1,
                    'alreadyNum' => $isGuest ? 0 : 1,
                ],
            ],
        ]);
        var_dump($data);exit;
        $view = \Yii::$app->view;
        $js = <<<JS
var dataStr = '$data';
var data = eval('(' + dataStr + ')');
JS;
        $view->registerJs($js, View::POS_HEAD);

        return $this->render('index');
    }

    /**
     * 秒杀Action
     */
    public function actionKill()
    {
        $sn = \Yii::$app->request->get('sn');
        return [
            'code' => 1,
            'message' => '秒杀成功',
            'ticket' => [
                'id' => 2,
                'sn' => '17122016',
                'ref_type' => 'PIKU',
                'ref_amount' => '10.00',
                'name' => '17122016' === $sn ? '飞利浦面包机' : '格力取暖器',
                'path' => '',
                'awardTime' => '2017-12-11 10:55:55',
            ],
        ];
    }

    /**
     * 秒杀成功记录列表
     */
    public function actionAwardList()
    {
        return [
            [
                'id' => 1,
                'sn' => '17122010',
                'ref_type' => 'PIKU',
                'ref_amount' => '10.00',
                'name' => '格力取暖器',
                'path' => '',
                'awardTime' => '2017-12-11 10:55:55',
            ],
            [
                'id' => 2,
                'sn' => '17122016',
                'ref_type' => 'PIKU',
                'ref_amount' => '10.00',
                'name' => '飞利浦面包机',
                'path' => '',
                'awardTime' => '2017-12-11 10:55:55',
            ],
        ];
    }
}

<?php

namespace wap\modules\promotion\controllers;

use common\models\promo\Reward;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\View;

class P171220Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 初始化页面
     */
    public function actionIndex()
    {
        $isGuest = Yii::$app->user->isGuest;
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171220']);
        $restTime = $this->getRestTime($promo);
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
        $view = Yii::$app->view;
        $js = <<<JS
var dataStr = '$data';
var data = eval('(' + dataStr + ')');
JS;
        $view->registerJs($js, View::POS_HEAD);
        return $this->render('index');
    }

    private function getRestTime($promo)
    {
        $nowTime = time();
        $startTime = strtotime($promo->startTime);
        if ($startTime > $nowTime) {
            return -1;
        }

        $recentOpenTime = Reward::find()
            ->where(['promo_id' => $promo->id])
            ->andFilterWhere(['>', 'createTime', date('Y-m-d H:i:s')])
            ->orderBy(['createTime' => SORT_ASC])
            ->limit(1)
            ->one();
        $lastOpenTime = ;
        //获得距离当前最近的一次秒杀商品未开始时间
        //获得最后一次秒杀商品开始时间
    }

    /**
     * 秒杀Action
     */
    public function actionKill()
    {
        $sn = Yii::$app->request->get('sn');
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

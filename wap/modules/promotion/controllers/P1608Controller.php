<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\order\OnlineOrder;
use common\models\user\Promo0809Log;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class P1608Controller extends Controller
{
    use HelpersTrait;

    /**
     * 邀请好友.
     */
    public function actionInvite()
    {
        $this->layout = false;
        return $this->render('invite');
    }

    /**
     * 奥运活动.
     */
    public function actionOlympic()
    {
        $this->layout = false;

        $promo = $this->findOr404(RankingPromo::class, ['key' => 'OLYMPIC_PROMO_160809']);
        $res = $this->getOlympicPromoRes($promo);

        if (5 === $res) {
            $log = Promo0809Log::find()
                ->where(['user_id' => $this->getAuthedUser()->id])
                ->andWhere(['between', 'createdAt', date('Y-m-d', $promo->startAt), date('Y-m-d', $promo->endAt)])
                ->one();
        } else {
            $log = null;
        }

        return $this->render('olympic', ['res' => $res, 'log' => $log]);
    }

    /**
     * 添加地址.
     */
    public function actionAddUserAddress()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'OLYMPIC_PROMO_160809']);
        $res = $this->getOlympicPromoRes($promo);

        if (4 === $res) {
            $log = new Promo0809Log([
                'user_id' => $this->getAuthedUser()->id,
                'createdAt' => date('Y-m-d'),
            ]);

            if ($log->load(Yii::$app->request->post()) && $log->validate()) {
                return ['code' => $log->save(false)];
            } else {
                return ['code' => false];
            }
        } else {
            return ['code' => false];
        }
    }

    private function getOlympicPromoRes(RankingPromo $promo)
    {
        $now = time();

        if ($promo->startAt > $now) {
            return 6;    //活动还未开始
        }

        if ($promo->endAt < $now) {
            return 7;   //活动已结束
        }

        if (Yii::$app->user->isGuest) {
            return 1;    //没有登录的情况
        }

        $user = $this->getAuthedUser();

        $logCount = Promo0809Log::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['between', 'createdAt', date('Y-m-d', $promo->startAt), date('Y-m-d', $promo->endAt)])
            ->count();

        if ($logCount) {
            return 5;   //已经领过奖品
        }

        $beforeOrdCount = OnlineOrder::find()
            ->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])
            ->andWhere(['<', 'order_time', $promo->startAt])
            ->count();

        if ($beforeOrdCount) {
            return 2;   //老用户
        }

        $ordCount = OnlineOrder::find()
            ->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])
            ->andWhere(['between', 'order_time', $promo->startAt, $promo->endAt])
            ->andWhere(['>=', 'order_money', 10000])
            ->count();

        if ($ordCount) {
            return 4;   //活动期间投资满10000
        }

        return 3;   //活动期间还没有投资的
    }
}

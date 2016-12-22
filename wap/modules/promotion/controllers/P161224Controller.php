<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\GoldenEgg;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class P161224Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    public function actionIndex($wx_share_key = null)
    {
        $share = null;
        $restTicket = 0;
        $user = $this->getAuthedUser();
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_golden_egg']);
        if (null !== $user) {
            try {
                $goldenEgg = new GoldenEgg($promo);
                $goldenEgg->addTicket($user, 'init', Yii::$app->request);
                $restTicket = $goldenEgg->getRestTicketCount($user);
            } catch (\Exception $e) {
                //DO NOTHING
            }
        }
        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('index', ['model' => $promo, 'restTicket' => $restTicket, 'share' => $share]);
    }

    public function actionDraw()
    {
        $rank = RankingPromo::findOne(['key' => 'promo_golden_egg']);

        if (\Yii::$app->user->isGuest) {
            return ['code' => 101, 'msg' => '蛋壳太硬了！登录后再砸吧！'];
        }

        try {
            $user = $this->getAuthedUser();
            $rank->isActive($user);
        } catch (\Exception $e) {
            return ['code' => 101, 'msg' => $e->getMessage()];
        }

        $goldenEgg = new GoldenEgg($rank);
        $user = $this->getAuthedUser();
        if (0 === $goldenEgg->getRestTicketCount($user)) {
            return ['code' => 102, 'msg' => '没有砸蛋机会了~ 快去投资吧！'];
        }

        try {
            $lottery = $goldenEgg->draw($user);
            return array_merge(['code' => 200], $goldenEgg::getAward($lottery->reward_id));
        } catch (\Exception $ex) {
            return ['code' => 400, 'msg' => '抽奖失败，请联系客服，客服电话' . \Yii::$app->params['contact_tel']];
        }

    }
}

<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\Promo1212;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class P1612Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 双十二抽奖活动.
     */
    public function actionDoubleTwelves($wx_share_key = null)
    {
        $share = null;
        $user = $this->getAuthedUser();
        $promo1212 = $this->promoInit();
        $boardList = $promo1212->getBoardList();

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        if (null === $user) {
            $tickets = 0;
        } else {
            $promo1212->addTicket($user, 'init', Yii::$app->request);
            $tickets = $promo1212->getRestTicketCount($user);
        }

        return $this->render('double_twelves', [
            'share' => $share,
            'boardList' => $boardList,
            'user' => $user,
            'tickets' => $tickets,
        ]);
    }

    /**
     * 用户抽奖.
     */
    public function actionDraw()
    {
        $user = $this->getAuthedUser();
        $promo1212 = $this->promoInit();
        $resp = $this->promoDateValidate($promo1212->promo, $user);

        if (empty($resp)) {
            if (null === $user) {
                $resp = ['code' => 1, 'message' => '您还未登录'];
            } else {
                $tickets = $promo1212->getRestTicketCount($user);

                if (!$tickets) {
                    $resp = ['code' => 1, 'message' => '您没有抽奖机会了'];
                } else {
                    $lottery = $promo1212->draw($user);
                    $resp = [
                        'code' => 0,
                        'data' => array_merge(Promo1212::getPrizeMessageConfig($lottery), ['tickets' => $tickets > 1 ? --$tickets : 0])
                    ];
                }
            }
        }

        return $resp;
    }

    /**
     * 用户中奖记录.
     */
    public function actionDrawForUser()
    {
        $user = $this->getAuthedUser();
        $promo1212 = $this->promoInit();
        $resp = $this->promoDateValidate($promo1212->promo, $user);

        if (empty($resp)) {
            if (null === $user) {
                $resp = ['code' => 1, 'message' => '您还未登录'];
            } else {
                $rewardList = $promo1212->getRewardList($user);

                if (empty($rewardList)) {
                    $resp = ['code' => 1, 'message' => '您还未抽过奖'];
                } else {
                    $this->layout = false;
                    $html = $this->render('_gift_list', ['rewardList' => $rewardList]);

                    $resp = ['code' => 0, 'html' => $html];
                }
            }
        }

        return $resp;
    }

    private function promoInit()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_12_12_21']);
        $promo1212 = new Promo1212($promo);

        return $promo1212;
    }

    private function promoDateValidate(RankingPromo $promo, $user = null)
    {
        $res = [];

        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $res = ['code' => 1, 'message' => $e->getMessage()];
        }

        return $res;
    }
}
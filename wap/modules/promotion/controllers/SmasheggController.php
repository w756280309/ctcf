<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

/**
 * 15亿限时砸金蛋活动.
 */
class SmasheggController extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';
    private $key = 'promo_070410_egg';

    /**
     * 活动主页.
     */
    public function actionIndex($wx_share_key = null)
    {
        $share = null;
        $promo = $this->fetchPromo();

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        $restTicket = 0;
        $userDraws = [];
        $user = $this->getAuthedUser();

        if ($user) {
            try {
                $promoClass = new $promo->promoClass($promo);

                $promoClass->addTicket($user, 'init', Yii::$app->request);
                $restTicket = $promoClass->getRestTicketCount($user);
            } catch (\Exception $e) {
                //DO NOTHING
            }
            $userDraws = $promoClass->getRewardList($user);
        }

        return $this->render('index', [
            'promo' => $promo,
            'restTicket' => $restTicket,
            'share' => $share,
            'userDraws' => $userDraws,
        ]);
    }

    /**
     * 抽奖.
     */
    public function actionDraw()
    {
        $promo = $this->fetchPromo();
        $user = $this->getAuthedUser();

        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            return [
                'code' => 102,
                'msg' => $e->getMessage(),
            ];
        }

        if (!$user) {
            return [
                'code' => 101,
                'msg' => '蛋壳太硬了！登录后再砸吧！',
            ];
        }

        $promoClass = new $promo->promoClass($promo);

        try {
            if (0 === $promoClass->getRestTicketCount($user)) {
                return [
                    'code' => 102,
                    'msg' => '没有砸蛋机会了~ 快去投资吧！',
                ];
            }

            $lottery = $promoClass->draw($user);
            $draw = $promoClass::getAward($lottery->reward_id);

            return [
                'code' => 200,
                'name' => $draw->name,
                'type' => $draw->ref_type,
            ];
        } catch (\Exception $ex) {
            return [
                'code' => 400,
                'msg' => '抽奖失败，请联系客服，客服电话'.\Yii::$app->params['platform_info.contact_tel'],
            ];
        }
    }

    private function fetchPromo()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => $this->key]);

        if (empty($promo->promoClass) || !class_exists($promo->promoClass)) {
            throw $this->ex404('活动模板类不存在');
        }

        return $promo;
    }
}
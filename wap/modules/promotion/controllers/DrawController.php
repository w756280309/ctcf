<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\Promo170603;
use common\models\promo\PromoLotteryTicket;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class DrawController extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';
    private $promo = null;
    private $promoKey = 'promo_170603';

    /**
     * 活动落地页.
     */
    public function actionIndex($wx_share_key = null)
    {
        $promo = $this->promo($this->promoKey);
        $share = $this->share($wx_share_key);
        $user = $this->getAuthedUser();

        $drawList = [];
        $ticketCount = null;
        $restTicketCount = null;
        $promoClass = new Promo170603($promo);
        if ($user) {
            $drawList = $promoClass->getRewardedList($user);
            $ticketCount = (int) PromoLotteryTicket::find()->where([
                'promo_id' => $promo->id,
                'user_id' => $user->id,
            ])->count();
            $restTicketCount = $promoClass->getRestTicketCount($user);
        }

        return $this->render('index', [
            'promo' => $promo,
            'share' => $share,
            'drawList' => $drawList,
            'ticketCount' => $ticketCount,
            'restTicketCount' => $restTicketCount,
            'user' => $user,
        ]);
    }

    /**
     * 抽奖.
     */
    public function actionDraw()
    {
        $promo = $this->promo($this->promoKey);
        $user = $this->getAuthedUser();

        $promoStatus = null;
        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }

        if (null !== $promoStatus) {
            $msg = 1 === $promoStatus ? '活动未开始' : '活动已结束';

            return $this->msg400($promoStatus, $msg);
        }

        if (null === $user) {
            return $this->msg400(3, '注册完就可以免费抽奖了哦!');
        }

        $dateTime = new \DateTime(date('Y-m-d H:i:s', $user->created_at));
        try {
            $promo->inPromoTime($dateTime);
        } catch (\Exception $e) {
            return $this->msg400(4, '本活动仅限新用户参与哦!快去参加其他活动吧!');
        }

        //抽奖
        $promoClass = new Promo170603($promo);
        try {
            $draw = $promoClass->draw($user);
        } catch (\Exception $e) {
            Yii::trace('拉新活动抽奖失败, 失败原因:'.$e->getMessage().', 用户: '.$user->id);
            return $this->msg400(5, '抽奖失败');
        }

        return $this->msg200('操作成功', [
            'drawId' => $this->getDrawId($draw),
            'drawName' => $draw->reward->name,
        ]);
    }

    /**
     * 分享落地页.
     */
    public function actionShare($wx_share_key = null)
    {
        $promo = $this->promo($this->promoKey);
        $share = $this->share($wx_share_key);

        return $this->render('share', [
            'promo' => $promo,
            'share' => $share,
        ]);
    }

    private function getDrawId(PromoLotteryTicket $ticket)
    {
        $config = [
            '603_coupon20' => 3,
            '603_coupon50' => 3,
            '603_packet1.66' => 4,
            '603_packet1.88' => 4,
            '603_USB' => 5,
            '603_chongdianbao' => 2,
            '603_card100' => 6,
            '603_card500' => 1,
            '603_appleWatch' => 7,
            '603_iphone7' => 0,
        ];

        return isset($config[$ticket->reward->sn]) ? $config[$ticket->reward->sn] : 4;
    }

    /**
     * 获取活动信息.
     */
    private function promo($key)
    {
        return $this->promo ?: $this->findOr404(RankingPromo::class, ['key' => $key]);
    }

    /**
     * 获取分享相关数据.
     */
    private function share($key)
    {
        $share = null;

        if (!empty($key)) {
            $share = Share::findOne(['shareKey' => $key]);
        }

        return $share;
    }

    private function msg400($code = 1, $msg = '操作失败')
    {
        Yii::$app->response->statusCode = 400;

        return [
            'code' => $code,
            'message' => $msg,
        ];
    }

    private function msg200($msg = '操作成功', array $data = [])
    {
        return [
            'code' => 0,
            'message' => $msg,
            'data' => $data,
        ];
    }
}
<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\PromoMidSummer;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class P1706Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 微博活动.
     */
    public function actionWeibo($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('weibo', [
            'share' => $share,
        ]);
    }

    /**
     * 父亲节活动.
     */
    public function actionFathersDay($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('fathers_day', [
            'share' => $share,
        ]);
    }

    /**
     * 微信绑定送积分.
     */
    public function actionWechatConnect($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('wechat_connect', [
            'share' => $share,
        ]);
    }

    /**
     * 双倍积分活动.
     */
    public function actionDoublePoints($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('double_points', [
            'share' => $share,
        ]);
    }

    /**
     * 夏至活动.
     */
    public function actionMidsummer($wx_share_key = null)
    {
        $share = null;
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170621']);
        $user = $this->getAuthedUser();
        $promoClass = new PromoMidSummer($promo);
        $restTicketCount = 0;
        $drawList = [];
        if ($user) {
            $drawList = $promoClass->getRewardedList($user);
            $restTicketCount = $promoClass->getRestTicketCount($user);
        }

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('midsummer', [
            'share' => $share,
            'promo' => $promo,
            'restTicketCount' => $restTicketCount,
            'drawList' => $drawList,
            'user' => $user,
        ]);
    }

    public function actionDraw()
    {
        $user = $this->getAuthedUser();
        $promo = RankingPromo::findOne(['key' => 'promo_170621']);
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
            return $this->msg400(2, '您还没有登录!');
        }

        $promoClass = new PromoMidSummer($promo);
        $restTicketCount = $promoClass->getRestTicketCount($user);
        if (0 === $restTicketCount) {
            return $this->msg400(3, '没有抽奖机会!');
        }

        try {
            $draw = $promoClass->draw($user);
        } catch (\Exception $e) {
            Yii::trace('拉新活动抽奖失败, 失败原因:'.$e->getMessage().', 用户: '.$user->id);
            return $this->msg400(4, $e->getMessage());
        }

        return $this->msg200('操作成功', [
            'amount' => $draw->reward->ref_amount,
        ]);
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

    /**
     * 积分抽奖送好礼活动
     */
    public function actionPointsDraw($redirect, $wx_share_key = null)
    {
        if (!$this->fromWx()) {
            return $this->redirect('/mall/portal/guest?dbredirect='.$redirect);
        }

        $share = null;
        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('@wap/views/site/app_download.php', [
            'share' => $share,
            'title' => '积分抽奖送礼',
        ]);
    }
}

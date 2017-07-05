<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\InviteRecord;
use common\models\promo\Promo170706;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class Draw0704Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';
    private $promo = null;
    private $promoKey = 'promo_170706';

    /**
     * 活动落地页.
     */
    public function actionIndex($wx_share_key = null)
    {
        $promo = $this->promo($this->promoKey);
        $share = null;
        $inviteCount = 0;
        $promoStatus = 0;
        $user = $this->getAuthedUser();

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }

        $drawList = [];
        $promoClass = new Promo170706($promo);
        if ($user) {
            $drawList = $promoClass->getRewardedList($user);
            $inviteCount = InviteRecord::getFriendsCountByUser($user, $promo->startTime, $promo->endTime);
        }

        return $this->render('index', [
            'share' => $share,
            'user' => $user,
            'drawList' => $drawList,
            'inviteCount' => $inviteCount,
            'promoStatus' => $promoStatus,
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

            return $this->msg400(3, $msg);
        }

        if (null === $user) {
            return $this->msg400(3, '您还没有登录哦!');
        }

        //抽奖
        $promoClass = new Promo170706($promo);
        try {
            $draw = $promoClass->draw($user);
        } catch (\Exception $e) {
            Yii::trace('拉新活动0706抽奖失败, 失败原因:'.$e->getMessage().', 用户: '.$user->id);

            $code = $e->getCode();

            if (0 === $code) {
                $code = 3;  //通过原始弹层弹出的
            }

            return $this->msg400($code, $e->getMessage());
        }

        return $this->msg200('抽奖成功', [
            'name' => $draw->reward->name,
            'imageUrl' => $draw->reward->path,
        ]);
    }

    /**
     * 获取活动信息.
     */
    private function promo($key)
    {
        return $this->promo ?: $this->findOr404(RankingPromo::class, ['key' => $key]);
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
            'prize' => $data,
        ];
    }
}

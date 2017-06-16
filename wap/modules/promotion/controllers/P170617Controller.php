<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\Callout;
use common\models\promo\CalloutResponder;
use common\models\promo\Promo170603;
use common\models\promo\PromoLotteryTicket;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;
use yii\web\Cookie;

class P170617Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';
    private $promo = null;
    private $promoKey = 'promo_170617';

    /**
     * 活动落地页.
     */
    public function actionIndex()
    {
        $promo = $this->promo($this->promoKey);
        $user = $this->getAuthedUser();
        $callout = null;
        $drawList = [];
        $ticketCount = null;
        $restTicketCount = null;
        $isPop = Yii::$app->session->has('isPop');
        if ($isPop) {
            Yii::$app->session->remove('isPop');
        }
        $promoClass = new Promo170603($promo);
        if (null !== $user) {
            $callout = Callout::find()
                ->where(['promo_id' => $promo->id])
                ->andWhere(['user_id' => $user->id])
                ->one();
            //尝试添加source为callout的ticket
            try {
                $promoClass->addTicket($user, $promoClass::SOURCE_CALLOUT);
            } catch (\Exception $ex) {
            }
            $drawList = $promoClass->getRewardedList($user);
            $ticketCount = (int) PromoLotteryTicket::find()->where([
                'promo_id' => $promo->id,
                'user_id' => $user->id,
            ])->count();
            $restTicketCount = $promoClass->getRestTicketCount($user);
        } else {
            if (Yii::$app->session->has('resourceOwnerId')) {
                $openId = Yii::$app->session->get('resourceOwnerId');
                $callout = Callout::find()
                    ->where(['promo_id' => $promo->id])
                    ->andWhere(['callerOpenId' => $openId])
                    ->one();
            }
        }

        return $this->render('index', [
            'promo' => $promo,
            'drawList' => $drawList,
            'ticketCount' => $ticketCount,
            'restTicketCount' => $restTicketCount,
            'user' => $user,
            'callout' => $callout,
            'isPop' => $isPop,
        ]);
    }

    /**
     * 抽奖.
     */
    public function actionDraw()
    {
        $promo = $this->promo($this->promoKey);
        $user = $this->getAuthedUser();

        //判断活动状态
        $promoStatus = $this->promoStatus($user);
        if (null !== $promoStatus) {
            $msg = 1 === $promoStatus ? '活动未开始' : '活动已结束';
            return $this->msg400($promoStatus, $msg);
        }

        //判断是否登录
        if (null === $user) {
            return $this->msg400(3, '注册完就可以免费抽奖了哦!');
        }

        //判断是否为活动参与用户
        $dateTime = new \DateTime(date('Y-m-d H:i:s', $user->created_at));
        try {
            $promo->inPromoTime($dateTime);
        } catch (\Exception $e) {
            return $this->msg400(4, '本活动仅限新用户参与哦!快去参加其他活动吧!');
        }

        //用户抽奖
        $promoClass = new Promo170603($promo);
        try {
            $draw = $promoClass->draw($user);
        } catch (\Exception $e) {
            Yii::trace('拉新活动抽奖失败, 失败原因:'.$e->getMessage().', 用户: '.$user->id);
            return $this->msg400(5, $e->getMessage());
        }

        return $this->msg200('操作成功', [
            'drawId' => $this->getDrawId($draw),
            'drawSource' => $draw->source,
            'drawName' => $draw->reward->name,
        ]);
    }

    /**
     * 分享落地页.
     *
     * a. 如果没有找到对应的用户报404；
     * b. 如果活动未开始，或已结束，进入页面立即弹框提示用户；
     * c. 如果该页面不是在微信打开的，进入页面弹框提示用户；
     * d. 成功进入页面以后，显示召集人手机号及当前召集人的召集进度；
     */
    public function actionShare()
    {
        //判断参数是否存在及是否能找到callout记录
        $request = Yii::$app->request->get();
        if (!isset($request['id'])
            || null === ($callout = Callout::findOne($request['id']))
        ) {
            throw $this->ex404();
        }

        //获得发起召集的用户
        $user = $this->findOr404(User::class, ['id' => $callout->user_id]);

        //判断当前活动状态
        $promoStatus = $this->promoStatus($user);

        //判断当前是否为微信
        $isWx = $this->fromWx();

        return $this->render('share', [
            'user' => $user,
            'isWx' => $isWx,
            'promoStatus' => $promoStatus,
            'callout' => $callout,
        ]);
    }

    /**
     * 用户发起助力
     */
    public function actionCall()
    {
        //判断是不是微信，不是微信
        if (!$this->fromWx()) {
            return $this->msg400(1, '当前页面不是在微信打开');
        }

        //判断用户是否登录
        $user = $this->getAuthedUser();
        if (null === $user) {
            return $this->msg400(1, '登录后才可以好友接力');
        }

        //判读是否存在open_id
        $openId = Yii::$app->session->get('resourceOwnerId');
        if (null === $openId) {
            return $this->msg400();
        }

        //判断活动
        $promoStatus = $this->promoStatus($user);
        if (null !== $promoStatus) {
            $msg = 1 === $promoStatus ? '活动未开始' : '活动已结束';
            return $this->msg400($promoStatus, $msg);
        }

        //判断是否为活动参与用户
        $promo = $this->promo($this->promoKey);
        $lottery = PromoLotteryTicket::findLotteryByPromoId($promo->id)
            ->andWhere(['user_id' => $user->id])
            ->one();
        if (null === $lottery) {
            return $this->msg400(1, '非活动参与用户，快去参加活动吧！');
        }

        //判断是否已经发起过召集
        $callout = Callout::find()
            ->where(['promo_id' => $promo->id])
            ->andWhere(['user_id' => $user->id])
            ->one();

        if (null !== $callout) {
            return $this->msg200('发起成功');
        }

        $endTime = new \DateTime($promo->endTime);
        $flag = Callout::initNew($user, $endTime, $promo->id, $openId)->save(false);
        if (!$flag) {
            return $this->msg400();
        }

        Yii::$app->session->set('isPop', true);
        return $this->msg200('发起成功');
    }

    /**
     * 点击助力action
     * get方法：
     * 参数：
     *      id    - 召集ID
     * a. 判断活动未开始或已结束，弹框提示用户；
     * b. 判断当前页面是否在微信中打开，如果不是，则弹框提示用户；
     * c. 判断openId是否获取成功，如果未成功，提示用户刷新重试，关闭弹框并刷新；
     * d. 判断召集人是否存在，如果不存在，提示用户系统繁忙，请稍后重试；
     * e. 判断是否在有效的召集时间段内，如果超出该时间段，弹框提示用户；
     * f. 判断召集人召集人数是否已达标，如果达标，弹框提示用户；
     * g. 判断用户是否已点击过助力按钮，如果已点击过，弹框提示用户；
     * h. 如果插入数据库失败，弹框提示用户系统繁忙，请稍后重试；
     */
    public function actionSupport()
    {
        //活动状态提示
        $promoStatus = $this->promoStatus();
        if (null !== $promoStatus) {
            $msg = 1 === $promoStatus ? '活动未开始' : '活动已结束';
            return $this->msg400($promoStatus, $msg);
        }

        //判断是不是微信，不是微信
        if (!$this->fromWx()) {
            return $this->msg400(3, '当前页面不是在微信打开');
        }

        //判断session里是否有open_id
        if (!Yii::$app->session->has('resourceOwnerId')) {
            return $this->msg400(4, '缺少open_id');
        }
        $openId = Yii::$app->session->get('resourceOwnerId');

        //判断请求参数及是否发起了召集
        $request = Yii::$app->request->get();
        $calloutId = isset($request['id']) ? (int) $request['id'] : false;
        if (!$calloutId
            || null === ($callout = Callout::findOne($calloutId))
        ) {
            return $this->msg400(5, '缺少参数');
        }

        //判断是否存在召集者
        $user = User::findOne($callout->user_id);
        if (null === $user) {
            return $this->msg400(6, '召集人不存在');
        }

        //判断召集者是否给自己助力了
        if ($callout->callerOpenId === $openId) {
            return $this->msg400(12, '不能给自己助力');
        }

        //检查是否已经好友已经助力成成功
        if ($callout->responderCount >= 1) {
            return $this->msg400(8, '好友已经助力成功');
        }

        //判断当前时间是否超过截止时间召集截止时间
        $endTime = new \DateTime($callout->endTime);
        $nowTime = new \DateTime();
        if ($endTime <= $nowTime) {
            return $this->msg400(9, '超过召集截止时间');
        }

        //检查该人是否已经为召集者点过助力
        $promo = $this->promo($this->promoKey);
        $c = Callout::tableName();
        $rc = CalloutResponder::tableName();
        $responder = CalloutResponder::find()
            ->innerJoin($c, "$c.id = $rc.callout_id")
            ->where(["$c.promo_id" => $promo->id])
            ->andWhere(["$rc.openid" => $openId])
            ->andWhere(["$rc.callout_id" => $callout->id])
            ->one();
        if (null !== $responder) {
            return $this->msg400(10, '已经点过助力');
        }

        //添加响应人数并更新召集记录
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            CalloutResponder::initNew($openId, $callout)->save(false);
            $sql = "update callout set responderCount = responderCount + 1 where promo_id = :promoId and id = :calloutId";
            $db->createCommand($sql, [
                'promoId' => $promo->id,
                'calloutId' => $callout->id,
            ])->execute();
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();

            return $this->msg400(11, '助力失败');
        }

        return $this->msg200('助力成功', ['responderCount' => $callout->responderCount + 1]);
    }

    private function getDrawId(PromoLotteryTicket $ticket)
    {
        $config = [
            '617_coupon20' => 3,
            '617_coupon50' => 3,
            '617_packet1.66' => 4,
            '617_packet1.88' => 4,
            '617_USB' => 5,
            '617_chongdianbao' => 2,
            '617_card100' => 6,
            '617_card500' => 1,
            '617_appleWatch' => 7,
            '617_iphone7' => 0,
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

    private function promoStatus($user = null)
    {
        $promo = $this->promo($this->promoKey);
        $promoStatus = null;
        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }

        return $promoStatus;
    }
}

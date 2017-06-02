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

        $callout = null;

        if ($user) {
            $callout = Callout::find()
                ->where(['promo_id' => $promo->id])
                ->andWhere(['user_id' => $user->id])
                ->one();
        }

        $drawList = [];
        $ticketCount = null;
        $restTicketCount = null;
        $promoClass = new Promo170603($promo);
        if ($user) {
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
        }

        return $this->render('index', [
            'promo' => $promo,
            'share' => $share,
            'drawList' => $drawList,
            'ticketCount' => $ticketCount,
            'restTicketCount' => $restTicketCount,
            'user' => $user,
            'callout' => $callout,
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
            return $this->msg400(5, $e->getMessage());
        }

        if ('register' === $draw->source) {
            Yii::$app->session->set('calloutUser', $user->id);
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
     * 	a. 如果没有找到对应的用户报404；
        b. 如果活动未开始，或已结束，进入页面立即弹框提示用户；
        c. 如果该页面不是在微信打开的，进入页面弹框提示用户；
        d. 成功进入页面以后，显示召集人手机号及当前召集人的召集进度；
     */
    public function actionShare()
    {
        //判断是否存在ucode - 用户标识码
        $request = Yii::$app->request->get();
        if (!isset($request['ucode'])) {
            throw $this->ex404();
        }

        $user = $this->findOr404(User::class, ['usercode' => $request['ucode']]);

        $promo = $this->promo($this->promoKey);
        $promoStatus = null;
        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }

        $openId = '';
        $isWx = $this->fromWx();

        //如果不存在open_id，申请授权获得open_id
        if (!Yii::$app->session->has('openId')) {
            $wxClient = Yii::$container->get('wxClient');
            $url = $wxClient->getAuthorizationUrl(Yii::$app->request->absoluteUrl);
            $code = Yii::$app->request->get('code');
            //判断是否有临时码及记录是否申请授权
            //若有，获得open_id，并存储到session中
            if ($code && Yii::$app->session->has('isApplied')) {
                try {
                    $response = $wxClient->getGrant($code);
                } catch (\Exception $ex) {
                    //do nothing
                }
                $openId = isset($response['resource_owner_id']) ? $response['resource_owner_id'] : '';
            } else {
                //没有临时码及未申请授权，需要去微信申请
                if (!$code && !Yii::$app->session->has('isApplied')) {
                    Yii::$app->session->set('isApplied', true);
                    $this->redirect($url);
                }
            }
        }

        $callout = null;

        //open_id不为空，应初始化召集记录
        if ('' !== $openId) {
            Yii::$app->session->set('openId', $openId);
            $callout = Callout::find()
                ->where(['promo_id' => $promo->id])
                ->andWhere(['user_id' => $user->id])
                ->one();

            //添加callout
            if (null === $callout) {
                $endTime = new \DateTime($promo->endTime);
                Callout::initNew($user, $endTime, $promo->id)->save();
            }
        }

        if (null === $callout) {
            $callout = Callout::find()
                ->where(['promo_id' => $promo->id])
                ->andWhere(['user_id' => $user->id])
                ->one();
        }

        return $this->render('share', [
            'user' => $user,
            'isWx' => $isWx,
            'promoStatus' => $promoStatus,
            'callout' => $callout,
        ]);
    }

    /**
     * 点击助力action
     * get方法：
     * 参数：
     *      code    - 用户标识码
     *      callout - 召集ID
     * 	a. 判断活动未开始或已结束，弹框提示用户；
        b. 判断当前页面是否在微信中打开，如果不是，则弹框提示用户；
        c. 判断openId是否获取成功，如果未成功，提示用户刷新重试，关闭弹框并刷新；
        d. 判断召集人是否存在，如果不存在，提示用户系统繁忙，请稍后重试；
        e. 判断是否在有效的召集时间段内，如果超出该时间段，弹框提示用户；
        f. 判断召集人召集人数是否已达标，如果达标，弹框提示用户；
        g. 判断用户是否已点击过助力按钮，如果已点击过，弹框提示用户；
        h. 如果插入数据库失败，弹框提示用户系统繁忙，请稍后重试；
     */
    public function actionSupport()
    {
        $promo = $this->promo($this->promoKey);

        $promoStatus = null;
        try {
            $promo->isActive();
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }

        if (null !== $promoStatus) {
            $msg = 1 === $promoStatus ? '活动未开始' : '活动已结束';

            return $this->msg400($promoStatus, $msg);
        }

        //判断是不是微信，不是微信
        if (!$this->fromWx()) {
            return $this->msg400(3, '当前页面不是在微信打开');
        }

        //判断session里是否有open_id
        if (!Yii::$app->session->has('openId')) {
            return $this->msg400(4, '缺少open_id');
        }

        //判断是否发起了召集
        $request = Yii::$app->request->get();
        $calloutId = isset($request['callout_id']) ? (int) $request['callout_id'] : false;
        $code = isset($request['code']) ? trim($request['code']) : '';

        //判断是否存在响应的两个参数
        if (!$calloutId || '' === $code) {
            return $this->msg400(5, '缺少参数');
        }

        //判断是否存在召集者
        $user = User::findOne(['usercode' => $code]);
        if (null === $user) {
            return $this->msg400(6, '召集人不存在');
        }

        $callout = Callout::find()
            ->where(['id' => $calloutId])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['promo_id' => $promo->id])
            ->one();

        //严格检查是否存在对应的召集记录
        if (null === $callout) {
            return $this->msg400(7, '召集记录不存在');
        }

        //检查是否已经好友已经助力成成功
        if ($callout->responderCount >= 3) {
            return $this->msg400(8, '好友已经助力成功');
        }

        //判断当前时间是否超过截止时间召集截止时间
        $endTime = new \DateTime($callout->endTime);
        $nowTime = new \DateTime();
        if ($endTime <= $nowTime) {
            return $this->msg400(9, '超过召集截止时间');
        }

        //检查该人是否已经点过助力
        $openId = Yii::$app->session->get('openId');
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
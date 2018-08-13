<?php

namespace wap\modules\ctcf\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\ShareLog;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\thirdparty\SocialConnect;
use common\models\user\User;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;
use yii\web\View;

class BaseController extends Controller
{
    const STATUS_SUCCESS = 0; //抽奖成功
    const ERROR_CODE_NOT_BEGIN = 1; //未开始
    const ERROR_CODE_ALREADY_END = 2; //已结束
    const ERROR_CODE_NOT_LOGIN = 3; //未登录
    const ERROR_CODE_NO_TICKET = 4; //无有效抽奖机会
    const ERROR_CODE_TODAY_NO_TICKET = 5; //无有效抽奖机会且今日未获得抽奖机会
    const ERROR_CODE_SYSTEM = 6; //系统错误（包括参数（promoKey）错误及抽奖其他错误）
    const ERROR_CODE_NEVER_GOT_TICKET = 7; //活动至今为止从未获得抽奖机会
    const ERROR_CODE_NO_ENOUGH_POINT = 8;  //用户积分不足
    const ERROR_CODE_SYSTEM_BUSY = 9;      //系统繁忙
    const ERROR_CODE_NO_ENOUGH_WORDS = 10;   //字数不足
    const ERROR_CODE_WORDS_EXCEEDING = 11;   //字数溢出
    const ERROR_CODE_HAS_CHECKED = 23000;    //已有抽奖机会

    use HelpersTrait;

    /**
     * 抽奖Action
     *
     * @return array
     */
    public function actionDraw()
    {
        //判断活动参数
        $key = Yii::$app->request->get('key');
        if (empty($key) || null === ($promo = RankingPromo::findOne(['key' => $key]))) {
            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }

        //判断活动状态
        $promoStatus = null;
        $user = $this->getAuthedUser();
        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }
        if (null !== $promoStatus) {
            return 1 === $promoStatus
                ? $this->getErrorByCode(self::ERROR_CODE_NOT_BEGIN)
                : $this->getErrorByCode(self::ERROR_CODE_ALREADY_END);
        }

        //判断活动状态
        if (null === $user) {
            return $this->getErrorByCode(self::ERROR_CODE_NOT_LOGIN);
        }

        //没有可用抽奖机会且当天未获得奖品
        $activeTicketExist = null === PromoLotteryTicket::fetchOneActiveTicket($promo, $user);
        $query = PromoLotteryTicket::findLotteryByPromoId($promo->id)
            ->andWhere(['user_id' => $user->id]);
        $cQuery = clone $query;
        $allTicketCount = (int) $cQuery->count();
        $extra = ['allTicketCount' => $allTicketCount];
        if (0 === $allTicketCount) {
            return $this->getErrorByCode(self::ERROR_CODE_NEVER_GOT_TICKET, $extra);
        }
        $todayNoTicket = null === $query
                ->andWhere(['date(from_unixtime(created_at))' => date('Y-m-d')])
                ->one();
        if ($activeTicketExist && $todayNoTicket) {
            return $this->getErrorByCode(self::ERROR_CODE_TODAY_NO_TICKET, $extra);
        }

        //抽奖
        try {
            $ticket = PromoService::draw($promo, $user);
            Yii::$app->response->statusCode = 200;
            return [
                'code' => self::STATUS_SUCCESS,
                'message' => '成功',
                'ticket' => $ticket->reward,
                'allTicketCount' => $allTicketCount,
            ];
        } catch (\Exception $e) {
            Yii::trace('奖励活动天天领抽奖失败, 失败原因:'.$e->getMessage().', 用户: '.$user->id);
            $code = $e->getCode();
            if (3 === $code || 4 === $code) {
                return $this->getErrorByCode($code, $extra);
            } else {
                return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
            }
        }
    }

    /**
     * 活动奖品列表
     *
     * @return array
     */
    public function actionAwardList($sn = null)
    {
        $key = Yii::$app->request->get('key');
        $user = $this->getAuthedUser();
        if (empty($key) || null === ($promo = RankingPromo::findOne(['key' => $key])) || null === $user) {
            return [];
        }

        if (!empty($promo->promoClass)) {
            $promoClass = new $promo->promoClass($promo);
            if (method_exists($promoClass, 'getAwardList')) {
                return $promoClass->getAwardList($user, $sn);
            }
        }

        return [];
    }

    /**
     * 获取用户积分的公共接口
     */
    public function actionGetPoints()
    {
        $user = $this->getAuthedUser();
        if ($user === null) {
            return $this->getErrorByCode(self::ERROR_CODE_NOT_LOGIN);
        }

        return [
            'code' => self::STATUS_SUCCESS,
            'message' => '成功',
            'points' => $user->points,
        ];
    }

    protected function getErrorByCode($code, Array $extra = [])
    {
        Yii::$app->response->statusCode = 400;
        $errors = self::getErrors();
        if (!in_array($code, array_keys($errors))) {
            $code = self::ERROR_CODE_SYSTEM;
        }
        $errorInfo = $errors[$code];
        if (!empty($extra)) {
            $errorInfo = array_merge($errorInfo, $extra);
        }

        return $errorInfo;
    }

    protected static function getErrors()
    {
        return [
            self::ERROR_CODE_NOT_BEGIN => [
                'code' => 1,
                'message' => '活动未开始',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_ALREADY_END => [
                'code' => 2,
                'message' => '活动已结束',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_NOT_LOGIN => [
                'code' => 3,
                'message' => '您还没有登录哦！',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_NO_TICKET => [
                'code' => 4,
                'message' => '您还没有抽奖机会哦！',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_TODAY_NO_TICKET => [
                'code' => 5,
                'message' => '您今日还没有获得抽奖机会，快去完成任务吧！',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_SYSTEM => [
                'code' => 6,
                'message' => '系统错误，请刷新重试',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_NEVER_GOT_TICKET => [
                'code' => 7,
                'message' => '您还未获得过任何抽奖机会哦！',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_NO_ENOUGH_POINT => [
                'code' => 8,
                'message' => '积分不足',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_SYSTEM_BUSY => [
                'code' => 9,
                'message' => '系统繁忙',
                'ticket' => null,
                'allTicketCount' => 0,
            ],
            self::ERROR_CODE_NO_ENOUGH_WORDS => [
                'code' => 10,
                'message' => '字数不足',
            ],
            self::ERROR_CODE_WORDS_EXCEEDING => [
                'code' => 11,
                'message' => '字数溢出',
            ],
            self::ERROR_CODE_HAS_CHECKED => [
                'code' => 23000,
                'message' => '已有抽奖机会',
            ]
        ];
    }

    protected function getPromoStatus(RankingPromo $promo, $timeAt = null)
    {
        $promoStatus = 0;
        $user = $this->getAuthedUser();
        try {
            $promo->isActive($user, $timeAt);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }

        return $promoStatus;
    }

    protected function getPromoAnnualInvest(RankingPromo $promo, $user)
    {
        $totalMoney = 0;
        $startTime = new \DateTime($promo->startTime);
        $endTime = new \DateTime($promo->endTime);
        if (null !== $user) {
            $totalMoney = UserInfo::calcAnnualInvest($user->id, $startTime->format('Y-m-d'), $endTime->format('Y-m-d'));
        }

        return $totalMoney;
    }

    protected function registerPromoStatusInView($promo)
    {
        $promoStatus = $this->getPromoStatus($promo);
        $view = \Yii::$app->view;
        $view->params['promoStatus'] = $promoStatus;
    }

    /**
     * 获取微信头像和昵称
     *
     * @param string $id 微信OpenId
     *
     * @return array
     */
    public function getWxInfo($id)
    {
        $data = [
            'headImgUrl' => null,
            'nickName' => null,
        ];

        $connect = SocialConnect::findOne(['user_id' => $id]);
        if (null !== $connect) {
            try {
                $app = Yii::$container->get('weixin_wdjf');
                $info = $app->user->get($connect->resourceOwner_id);
                if (isset($info['openid'])) {
                    return ['headImgUrl' => $info->headimgurl, 'nickName' => $info->nickname];
                }
            } catch (\Exception $ex) {
                //不允许获得不到微信头像及昵称时报错
            }
        }

        return $data;
    }

    /**
     * 批量获取微信信息
     *
     * @param array $openIds 微信OpenId集合
     *
     * @return array
     */
    public function getWxsInfo(Array $openIds)
    {
        $data = [];
        if (empty($openIds)) {
            return $data;
        }

        try {
            $openIds = array_values($openIds);
            $app = Yii::$container->get('weixin_wdjf');
            $infos = $app->user->batchGet($openIds);
            if (isset($infos['errcode'])) {
                return $data;
            }

            return $infos;
        } catch (\Exception $ex) {
            //不允许获得不到微信头像及昵称时报错
        }

        return $data;
    }

    /**
     * 将要返回的信息在页面中生成为一个JSON对象
     *
     * @param array $data 待返回页面的信息数组
     *
     * @return void
     */
    public function renderJsInView(Array $data = [])
    {
        $data = json_encode($data);
        $view = Yii::$app->view;
        $js = <<<JS
var dataStr = '$data';
var dataJson = eval('(' + dataStr + ')');
JS;
        $view->registerJs($js, View::POS_HEAD);
    }

    /**
     * 检查活动状态及登录状态
     *
     * @param null|RankingPromo $promo 活动对象
     * @param null|User $user 用户对象
     *
     * @return bool
     * @throws \Exception
     */
    protected function checkStatus($promo, $user)
    {
        if (null === $promo) {
            throw new \Exception('活动不存在', self::ERROR_CODE_SYSTEM);
        }
        $promo->isActive($user);
        //判断用户状态
        if (null === $user) {
            throw new \Exception('用户未登录', self::ERROR_CODE_NOT_LOGIN);
        }

        return true;
    }

    /**
     * 添加分享记录
     *
     * @param string $shareUrl 分享URL
     * @param string $scene 场景
     *
     * @return void
     */
    public function actionAddShare($shareUrl, $scene)
    {
        $user = Yii::$app->user->getIdentity();
        if (null === $user) {
            return false;
        }

        $now = date("Y-m-d", time());
        $ipAddress = Yii::$app->request->getUserIP();
        $newShareLog = new ShareLog();
        $newShareLog->shareUrl = $shareUrl;
        $newShareLog->scene = $scene;
        $newShareLog->userId = $user->id;
        $newShareLog->ipAddress = $ipAddress;
        $newShareLog->createdAt = $now;
        $newShareLog->save(false);

        return true;
    }
}

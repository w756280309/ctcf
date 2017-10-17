<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

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

    use HelpersTrait;

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
        $query = PromoLotteryTicket::findLotteryByPromoId($promo->id)->andWhere(['user_id' => $user->id]);
        $cQuery = clone $query;
        if (0 === (int) $cQuery->count()) {
            return $this->getErrorByCode(self::ERROR_CODE_NEVER_GOT_TICKET);
        }
        $todayNoTicket = null === $query->andWhere(['date(from_unixtime(created_at))' => date('Y-m-d')])->one();
        if ($activeTicketExist && $todayNoTicket) {
            return $this->getErrorByCode(self::ERROR_CODE_TODAY_NO_TICKET);
        }

        //抽奖
        try {
            $ticket = PromoService::draw($promo, $user);
            Yii::$app->response->statusCode = 200;
            return [
                'code' => self::STATUS_SUCCESS,
                'message' => '成功',
                'ticket' => $ticket->reward,
            ];
        } catch (\Exception $e) {
            Yii::trace('奖励活动天天领抽奖失败, 失败原因:'.$e->getMessage().', 用户: '.$user->id);
            $code = $e->getCode();
            if (3 === $code || 4 === $code) {
                return $this->getErrorByCode($code);
            } else {
                return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
            }
        }
    }

    public function actionAwardList()
    {
        $key = Yii::$app->request->get('key');
        $user = $this->getAuthedUser();
        if (empty($key) || null === ($promo = RankingPromo::findOne(['key' => $key])) || null === $user) {
            return [];
        }

        if (!empty($promo->promoClass)) {
            $promoClass = new $promo->promoClass($promo);
            if (method_exists($promoClass, 'getAwardList')) {
                return $promoClass->getAwardList($user);
            }
        }

        return [];
    }

    private function getErrorByCode($code)
    {
        Yii::$app->response->statusCode = 400;
        $errors = self::getErrors();
        if (!in_array($code, array_keys($errors))) {
            $code = self::ERROR_CODE_SYSTEM;
        }

        return $errors[$code];
    }

    private static function getErrors()
    {
        return [
            self::ERROR_CODE_NOT_BEGIN => [
                'code' => 1,
                'message' => '活动未开始',
                'ticket' => null,
            ],
            self::ERROR_CODE_ALREADY_END => [
                'code' => 2,
                'message' => '活动已结束',
                'ticket' => null,
            ],
            self::ERROR_CODE_NOT_LOGIN => [
                'code' => 3,
                'message' => '您还没有登录哦！',
                'ticket' => null,
            ],
            self::ERROR_CODE_NO_TICKET => [
                'code' => 4,
                'message' => '您还没有抽奖机会哦！',
                'ticket' => null,
            ],
            self::ERROR_CODE_TODAY_NO_TICKET => [
                'code' => 5,
                'message' => '您今日还没有获得抽奖机会，快去完成任务吧！',
                'ticket' => null,
            ],
            self::ERROR_CODE_SYSTEM => [
                'code' => 6,
                'message' => '系统错误，请刷新重试',
                'ticket' => null,
            ],
            self::ERROR_CODE_NEVER_GOT_TICKET => [
                'code' => 7,
                'message' => '您还未获得过任何抽奖机会哦！',
                'ticket' => null,
            ],
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
}

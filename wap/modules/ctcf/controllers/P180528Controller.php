<?php

namespace wap\modules\ctcf\controllers;

use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\TicketToken;
use wap\modules\promotion\models\RankingPromo;
use yii\db\Exception;
use Yii;

class P180528Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    const SOURCE_APPOINTMENT = 'appointment';
    const SOURCE_SECOND = 'second';
    const SOURCE_RED_PACKET = 'red_packet';
    const DAILY_SECOND_NUM = 100;
    /**
     *  三周年庆预热活动,初始化页面
     *  接口地址：/ctcf/p180528/index
     *  返回字段：
     *  promoStatus: [1,2]   1,活动未开始  2,活动已结束
     *  isLoggedIn : [true,false]   true,已登录  false,未登录
     *  isAppointment: [true,false]  true:已预约  false：未预约
     *  isSecond: [true,false] true:已秒杀  false：未秒杀
     *  redPacket: [
     *      'code' => [20,0,22,23,24],
     *      'message' => ['10点场即将开始，红包雨进行中，16点场即将开始，今日红包雨已结束,已抽奖']
     * ]
     */
    public function actionIndex()
    {
        //周年庆预热预约和红包雨
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180528']);
        $user = $this->getAuthedUser();
        //周年庆活动
        $mainPromo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180601']);
        $secondPromo = $this->findOr404(RankingPromo::class, ['key' => 'promo_1805282']);
        $secondPromoClass = new $secondPromo->promoClass($secondPromo);
        $promoClass = new $promo->promoClass($promo);
        $isAppointment = false;
        $isSecond = false;
        $day = date('Ymd');
        $redPacketStatus = $promoClass->getActiveStatus($user, 'init');
        if ($user) {
            $isAppointment = null !== $promoClass->getOneActiveTicket($mainPromo->id, $user, self::SOURCE_APPOINTMENT);
            $redis = \Yii::$app->redis;
            $activeSecondNum = $redis->LLEN('secondNum' . $day);
            if ($activeSecondNum >= self::DAILY_SECOND_NUM) {
                $isSecond = true;
            } else {
                $isSecond =  null !== $secondPromoClass->fetchOneDrawnTicket($user, self::SOURCE_SECOND);
            }
        }

        $data = [
            'promo_id' =>$promo->id,
            'promoStatus' => $this->getPromoStatus($promo),
            'isLoggedIn' => null !== $user,
            'isAppointment' => $isAppointment,
            'isSecond' => $isSecond,
            'redPacket' => $redPacketStatus,
        ];

        $this->renderJsInView($data);

        return $this->render('index');
    }

    /**
     * 预约接口
     * 接口地址：/ctcf/p180528/appointment
     * 返回字段：
     * [
     *      'code' => [0,1,2,3,23000],
     *      'message' => ['进行中','未开始','已结束','未登录','已预约']
     * ]
     */
    public function actionAppointment()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180528']);
        $mainPromo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180601']);
        $user = $this->getAuthedUser();
        try {
            $this->checkStatus($promo, $user);
            $key = $mainPromo->id . '-' . $user->id . '-' . self::SOURCE_APPOINTMENT;
            TicketToken::initNew($key)->save(false);
            PromoLotteryTicket::initNew($user, $mainPromo, self::SOURCE_APPOINTMENT)->save(false);
            return [
                'code' => 0,
                'message' => '预约成功',
            ];
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            return $this->getErrorByCode($code);
        }
    }

    /**
     * 秒杀接口
     * 接口地址：/ctcf/p180528/second
     * 返回字段：
     * [
     *      'code' => [0,1,2,3,12,23000],
     *      'message' => ['进行中','未开始','已结束','未登录',‘数量不足’,'已秒完']
     * ]
     */
    public function actionSecond()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_1805282']);
        $user = $this->getAuthedUser();
        $day = date('Ymd');
        $redis = \Yii::$app->redis;
        $redis->expire('secondNum' . $day, 60 * 60 * 24);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->checkStatus($promo, $user);
            $activeSecondNum = $redis->LLEN('secondNum' . $day);
            if ($activeSecondNum >= self::DAILY_SECOND_NUM) {
                throw new \Exception('已秒完', 10);
            }
            $key = $promo->id . '-' . $user->id . '-' . self::SOURCE_SECOND . '-' .$day;
            TicketToken::initNew($key)->save(false);
            PromoLotteryTicket::initNew($user, $promo, self::SOURCE_SECOND)->save(false);
            PromoService::draw($promo, $user);
            $transaction->commit();
            $redis->LPUSH('secondNum' . $day, $user->id);
            return [
                'code' => 0,
                'message' => '已秒完',
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $code = $ex->getCode();
            return $this->getErrorByCode($code);
        }
    }

    /**
     * 红包雨入口接口
     * 接口地址：/ctcf/p180528/open
     * 返回字段：
     * [
     *      'code' => [0,1,2,3,24，20,22,23],
     *      'message' => ['进行中','未开始','已结束','未登录','已抽奖','10点场即将开启'，'16点场即将开启','今日红包雨已结束'],
     * ]
     */
    public function actionOpen()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180528']);
        $user = $this->getAuthedUser();
        try {
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $result = $promoClass->getActiveStatus($user, 'click');
            return $result;
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            return $this->getErrorByCode($code);
        }
    }

    /**
     * 红包雨抽奖接口
     * 接口地址：/ctcf/p180528/red-packet
     * 返回字段：
     * [
     *      'code' => [true, false],
     *      'message' => ['成功', '失败'],
     *      'sn' => '180528_C20'
     * ]
     */
    public function actionRedPacket()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180528']);
        $user = $this->getAuthedUser();
        try {
            $day = date('Ymd');
            $hour = date('H');
            $hour = $hour < 16 ? 10 :16;
            $day .= $hour;
            $key = $promo->id . '-' . $user->id .'-' . self::SOURCE_RED_PACKET . $day;
            TicketToken::initNew($key)->save(false);
            PromoLotteryTicket::initNew($user, $promo, self::SOURCE_RED_PACKET)->save(false);
            $ticket = PromoService::draw($promo, $user);
            Yii::$app->response->statusCode = 200;
            return [
                'code' => true,
                'message' => '成功',
                'ticket' => $ticket->reward->sn,
            ];
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            return $this->getErrorByCode($code);
        }
    }
    //秒杀记录接口
    public function actionSecondList()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_1805282']);
        $promoClass = new $promo->promoClass($promo);
        $user = $this->getAuthedUser();
        if ($user) {
             return $promoClass->getAwardList($user);
        }
    }

    //红包雨记录接口
    public function actionRedPacketList()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180528']);
        $promoClass = new $promo->promoClass($promo);
        $user = $this->getAuthedUser();
        if ($user) {
            return $promoClass->getAwardList($user);
        }
    }
}
<?php

namespace wap\modules\promotion\controllers;

use common\models\adv\ShareLog;

use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class P180312Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /**
     * 植树节活动初始化页面
     *  isLoggedIn 判断登录状态，已登录：true,未登录：false
     *  promoStatus 判断活动状态：活动未开始1,活动已结束2,活动进行中：0
     * isReceived  判断每日免费浇水机会是否领取,已领取true,未领取false
     * isShared  判断当日是否已经分享到朋友圈，已分享true,未分享false
     * wateredCount  已浇水次数
     * unWateredCount 未浇水次数
     * addWateredCount  增加的浇水次数,当用户未登录或者浇水次数为0时，不弹出toast提示
     * rewards 奖池
     * id ： 奖品id
     * name: 奖品名称
     * status 奖品是否领取(awardStatus),已领取true,未领取false
     * 提交
     */
    public function actionIndex()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180312']);
        $user = $this->getAuthedUser();
        $isLoggedIn = null !== $user;
        $promoStatus = $this->getPromoStatus($promo);
        $wateredCount = 0;
        $unWateredCount = 0;
        $addWateredCount = 0;
        $ids = [];
        $rewards = Reward::find()
            ->select('id, name')
            ->where(['promo_id' => $promo->id])
            ->asArray()
            ->all();
        $isReceived = null;
        $isShared = null;
        if ($isLoggedIn && $promoStatus === 0) {
            $promoClass = new $promo->promoClass($promo);
            $isReceived = $promoClass->receive($user, 'free');
            $isShared = $promoClass->share($user, 'share');
            //获取用户所有的浇水次数，遍历获取已浇水，未浇水次数
            $tickets = PromoLotteryTicket::find()
                ->where(['promo_id' => $promo->id])
                ->andWhere(['user_id' => $user->id])
                ->all();
            foreach ($tickets as $ticket) {
                if ($ticket->isDrawn === 0) {
                    $unWateredCount++;
                    array_push($ids, $ticket->id);
                } elseif ($ticket->isDrawn === 1) {
                    $wateredCount++;
                }
            }
            foreach ($rewards as $key => $value) {
                $rewards[$key]['status'] = $promoClass->awardStatus($user, $value['id']);
            }

            $redis = Yii::$app->redis;
            $lastTicketId = $redis->hget('lastPromoTicketId', $user->id);
            if (count($ids)) {
                $promoClass->setRedis($user, max($ids));
            }
            //获取增加的未浇水次数
            $addWateredCount = PromoLotteryTicket::find()
                ->where(['promo_id' => $promo->id])
                ->andWhere(['user_id' => $user->id])
                ->andWhere(['isDrawn' => 0])
                ->andWhere(['>', 'id', $lastTicketId])
                ->count();

        }

        $data = [
            'isLoggedIn' => $isLoggedIn,
            'isReceived' => $isReceived,
            'isShared' => $isShared,
            'wateredCount' => $wateredCount,
            'unWateredCount' => $unWateredCount,
            'promoStatus' => $promoStatus,
            'addWateredCount' => $addWateredCount,
            'rewards' => $rewards,
        ];
        $this->renderJsInView($data);

        return $this->render('index');
    }

    /**
     * 获取每日免费浇水次数
     *
     * 提交方式：AJAX get
     * 地址：/promotion/p180312/get-free
     * 返回值：
     *      [
     *          'code' => 0,1,2,3,4
     *          'message' => '成功，未开始，已结束，未登录，失败',
     *      ]
     *
     * @return array
     */
    public function actionGetFree()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180312']);
        $user = $this->getAuthedUser();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $key = $promo->id . '-' . $user->id . '-free-' . date('m-d', time());
            TicketToken::initNew($key)->save(false);
            $ticket = PromoLotteryTicket::initNew($user, $promo, 'free');
            $id = $ticket->save(false);
            $transaction->commit();
            if ($id) {
                PromoLotteryTicket::find()
                    ->where(['user_id' => $user->id])
                    ->andWhere(['promo_id' => $promo]);
                $promoClass->setRedis($user, $ticket->id);
                return [
                    'code' => 0,
                    'message' => '成功',
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $code = $ex->getCode();
            if (23000 === (int)$code) {
                return [
                    'code' => '4',
                    'message' => '失败'
                ];
            }
            return $this->getErrorByCode($code);
        }
    }

    /**
     * 分享到朋友圈的接口为api/v1/app/Share/log
     * 此接口为分享后获取一次浇水机会的接口
     *
     * 提交方式： ajax get
     *
     * 地址： /promotion/p180312/get-share
     *
     *返回值：
     *      [
     *          'code' => 0,1,2,3,4
     *          'message' => '成功(增加一次浇水机会)，未开始，已结束，未登录，失败'
     *      ]
     */
    public function actionGetShare($shareUrl, $scene)
    {

        $promo = RankingPromo::findOne(['key' => 'promo_180312']);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = $this->getAuthedUser();
            $this->checkStatus($promo, $user);
            $now = date("Y-m-d", time());
            $ipAddress = Yii::$app->request->getUserIP();
            $newShareLog = new ShareLog();
            $newShareLog->shareUrl = $shareUrl;
            $newShareLog->scene = $scene;
            $newShareLog->userId = $user->id;
            $newShareLog->ipAddress = $ipAddress;
            $newShareLog->createdAt = $now;
            $newShareLog->save(false);
            $promoClass = new $promo->promoClass($promo);
            $key = $promo->id . '-' . $user->id . '-order-';
            TicketToken::initNew($key)->save(false);
            $ticket = PromoLotteryTicket::initNew($user, $promo, 'share');
            $id = $ticket->save(false);
            $transaction->commit();
            if ($id) {
                $promoClass->setRedis($user, $ticket->id);
                return [
                    'code' => 0,
                    'message' => '成功',
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $code = $ex->getCode();
            if (23000 === (int)$code) {
                return [
                    'code' => 4,
                    'message' => '失败',
                ];
            }
            return $this->getErrorByCode($code);
        }
    }

    /**
     * 领取奖励接口
     *
     * 提交方式：AJAX get
     * 地址：/promotion/p180312/get-award
     * 参数：
     *      id：奖品ID （int）
     * 返回值：
     *      [
     *          'code' => 0,1,2,3,4,5
     *          'message' => '领取成功，未开始，已结束，未登录，已领取，浇水次数不足',
     *      ]
     *
     * @return array
     */
    public function actionGetAward()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180312']);
        $user = $this->getAuthedUser();
        $rewardId = Yii::$app->request->get('id');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->checkStatus($promo, $user);
            //获取该用户此活动中的已浇水次数
            $wateredCount =  PromoLotteryTicket::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['promo_id' => $promo->id])
                ->andWhere(['isDrawn' => 1])
                ->count();
            $reward = Reward::findOne(['id' => $rewardId]);
            $limit = $this->getCount($reward->sn);
            if ($wateredCount >= (int)$limit) {
                    $key = $promo->id . '-' . $user->id . '-' . $reward->ref_type . '-' . $reward->id;
                    TicketToken::initNew($key)->save(false);
                    PromoService::award($user, $reward, $promo);
                    $transaction->commit();
                        return [
                            'code' => 0,
                            'message' => '领取成功'
                        ];
            } else {
                return [
                    'code' => 5,
                    'message' => "浇水次数不足,<br>请点击下方浇水按钮",
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $code = $ex->getCode();
            if (23000 === (int)$code) {
                return [
                    'code' => '4',
                    'message' => '已领取',
                ];
            }
            return $this->getErrorByCode($code);
        }
    }

    /**
     * 全部浇灌接口
     *
     * 提交方式 ajax get
     * 接口地址：/promotion/p180312/all-watered
     *
     * 返回值：
     *      [
     *          'code' => 0,1,2,3,6,7
     *          'message' => 成功，未开始，已结束，未登录，系统错误，没有浇水次数了',
     *          'count' => '全部浇灌次数，当code = 0时，返回成功的次数，其余状态返回null'
     *      ]
     */
    public function actionAllWatered()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180312']);
        $user = $this->getAuthedUser();
        try {
            $updateCount = PromoLotteryTicket::updateAll(['isDrawn' => 1], [
                'promo_id' => $promo->id,
                'user_id' => $user->id,
                'isDrawn' => 0]);
            if ($updateCount) {
                return [
                    'code' => 0,
                    'message' => '成功',
                    'count' => $updateCount
                ];
            } else {
                return [
                    'code' => 5,
                    'message' => '没有浇水次数了',
                    'count' => $updateCount,
                ];
            }

        } catch (\Exception $ex) {
            $code = $ex->getCode();
            return $this->getErrorByCode($code);
        }
    }
    //根据奖品的sn获取得到该奖品需要的浇水次数;
    private function getCount($sn)
    {
        $limit = null;
        switch ($sn) {
            case '180312_C3': {
                $limit = 2;
                break;
            }
            case '180312_C8': {
                $limit = 3;
                break;
            }
            case '180312_C10': {
                $limit = 5;
                break;
            }
            case '180312_C20': {
                $limit = 8;
                break;
            }
            case '180312_GJX': {
                $limit = 28;
                break;
            }
            case '180312_GLB': {
                $limit = 60;
                break;
            }
            case '180312_G50': {
                $limit = 80;
                break;
            }
        }
        return $limit;
    }
}

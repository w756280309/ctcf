<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\promo\PromoLotteryTicket;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use wap\modules\promotion\promo\Promo160707;
use Yii;
use yii\web\Controller;

class P160708Controller extends Controller
{
    use HelpersTrait;

    const PROMOKEY = 'PC_LAUNCH_160707';

    /**
     * 幸运砸金蛋页面.
     */
    public function actionIndex()
    {
        $this->layout = false;

        $model = $this->findOr404(RankingPromo::class, ['key' => self::PROMOKEY]);

        if (!Yii::$app->user->isGuest) {
            //初始化用户机会
            $promo = new Promo160707($model);

            if (date('Y-m-d') >= substr($promo->startTime, 0, 10) && date('Y-m-d') <= substr($promo->endTime, 0, 10)) {
                $promo->initTicket($this->getAuthedUser());
            }

            $drawNum = $this->getDrawNum();
        } else {
            $drawNum = [];
        }

        return $this->render('index', ['model' => $model, 'drawNum' => $drawNum]);
    }

    /**
     * 抽奖.
     */
    public function actionDraw()
    {
        $rank = $this->findOr404(RankingPromo::class, ['key' => self::PROMOKEY]);

        if (substr($rank->startTime, 0, 10) > date('Y-m-d')) {
            return ['code' => 102, 'msg' => '活动还未开始！'];
        }

        if (substr($rank->endTime, 0, 10) < date('Y-m-d')) {
            return ['code' => 102, 'msg' => '活动已结束！'];
        }

        if (\Yii::$app->user->isGuest) {
            return ['code' => 101, 'msg' => '蛋壳太硬了！登录后再砸吧！'];
        }

        $drawNum = $this->getDrawNum();
        if (0 === $drawNum['weichou']) {
            return ['code' => 102, 'msg' => '没有砸蛋机会了~ 快去投资吧！'];
        }

        $user = $this->getAuthedUser();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($user->userIsInvested) {
                if (0 === $drawNum['yichou']) {
                    $ticket = $this->draw($user->id, 3, self::PROMOKEY);
                    if ($this->rewardPrize($user, $ticket)) {
                        $transaction->commit();
                        return $this->getBackInfo(3);
                    } else {
                        throw new \Exception('发奖失败');
                    }
                } else {
                    $p = new Promo160707($rank);
                    $drawId = $p->drawReward($user);
                    $ticket = $this->draw($user->id, $drawId, self::PROMOKEY);
                    if ($this->rewardPrize($user, $ticket)) {
                        $transaction->commit();
                        return $this->getBackInfo($drawId);
                    } else {
                        throw new \Exception('发奖失败');
                    }
                }
            } else {
                if (0 === $drawNum['yichou']) {    //未投资用户首次必中28元券
                    $ticket = $this->draw($user->id, 1, self::PROMOKEY);
                    if ($this->rewardPrize($user, $ticket)) {
                        $transaction->commit();
                        return $this->getBackInfo(1);
                    } else {
                        throw new \Exception('发奖失败');
                    }
                } elseif (1 === $drawNum['yichou']) {     //第二次必中50元券
                    $ticket = $this->draw($user->id, 2, self::PROMOKEY);
                    if ($this->rewardPrize($user, $ticket)) {
                        $transaction->commit();
                        return $this->getBackInfo(2);
                    } else {
                        throw new \Exception('发奖失败');
                    }
                } elseif (2 === $drawNum['yichou']) {     //第三次必中90元券
                    $ticket = $this->draw($user->id, 3, self::PROMOKEY);
                    if ($this->rewardPrize($user, $ticket)) {
                        $transaction->commit();
                        return $this->getBackInfo(3);
                    } else {
                        throw new \Exception('发奖失败');
                    }
                } else {
                    throw new \Exception();
                }
            }
        } catch(\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 获取用户当前已抽奖次数以及未抽奖次数
     */
    private function getDrawNum()
    {
        $model = PromoLotteryTicket::findAll(['user_id' => $this->getAuthedUser()->id]);

        $data = ['weichou' => 0, 'yichou' => 0];
        foreach ($model as $val) {
            if ($val->isDrawn) {
                ++$data['yichou'];
            } else {
                ++$data['weichou'];
            }
        }

        return $data;
    }

    /**
     * 给用户发放代金券.
     */
    private function addCoupon(User $user, CouponType $coupon)
    {
        if (date('Y-m-d') < $coupon->issueStartDate || date('Y-m-d') > $coupon->issueEndDate) {
            throw new \Exception('发行日期异常');
        }

        $time = time();
        $model = new UserCoupon([    //expiryDate记录了代金券的有效结束时间,如果有效天数不为空,则以领用时间为起点计算有效结束时间,否则直接读取代金券的有效结束时间
            'couponType_id' => $coupon->id,
            'user_id' => $user->id,
            'isUsed' => 0,
            'created_at' => $time,
            'expiryDate' => empty($coupon->expiresInDays) ? $coupon->useEndDate : date('Y-m-d', $time + 24 * 60 * 60 * ($coupon->expiresInDays - 1)),
        ]);

        return $model->save(false);
    }

    /**
     * 抽奖.
     */
    private function draw($uid, $drawKey, $promoKey)
    {
        $ticket = PromoLotteryTicket::find()->where(['isDrawn' => 0, 'user_id' => $uid])->orderBy("id")->one();

        $ticket->isDrawn = 1;
        $ticket->reward_id = $drawKey;
        $ticket->ip = Yii::$app->request->userIP;
        $ticket->rewardedAt = time();

        if (!$ticket->save()) {
            throw new \Exception('数据库错误');
        }

        return $ticket;
    }

    /**
     * 发奖.
     */
    private function rewardPrize(User $user, PromoLotteryTicket $ticket)
    {
        if (!$ticket->isDrawn || empty($ticket->reward_id)) {
            return false;
        }

        if (9 === $ticket->reward_id) {
            return true;
        }

        $couponConfig = Promo160707::getCouponConfig();

        if (in_array($ticket->reward_id, [1, 2, 3, 4, 5])) {
            $coupon = CouponType::findOne(['sn' => $couponConfig[$ticket->reward_id]]);
            if (null === $coupon) {
                return false;
            }

            if (!$this->addCoupon($user, $coupon)) {
                return false;
            }
        } elseif (6 === $ticket->reward_id) {
            $drawArr = [1, 2, 3, 3, 3, 4, 4, 4, 5];
            foreach ($drawArr as $val) {
                $coupon = CouponType::findOne(['sn' => $couponConfig[$val]]);
                if (null === $coupon) {
                    return false;
                }

                if (!$this->addCoupon($user, $coupon)) {
                    return false;
                }
            }
        } elseif (7 === $ticket->reward_id) {
            $draw = new PromoLotteryTicket([
                'user_id' => $user->id,
                'isDrawn' => 0,
                'isRewarded' => 0,
                'source' => 3,
            ]);

            if (!$draw->save()) {
                return false;
            }
        }

        $ticket->isRewarded = 1;

        return $ticket->save();
    }

    /**
     * 获取返回结果.
     */
    private function getBackInfo($drawId)
    {
        $drawConfig = Promo160707::getDrawConfig();

        if (in_array($drawId, [1, 2, 3, 4, 5])) {
            return ['code' => 200, 'type' => 1, 'name' => $drawConfig[$drawId]];
        } elseif (6 === $drawId) {
            return ['code' => 200, 'type' => 2, 'name' => $drawConfig[$drawId]];
        } elseif (7 === $drawId) {
            return ['code' => 200, 'type' => 4, 'name' => $drawConfig[$drawId]];
        } elseif (8 === $drawId) {
            return ['code' => 300, 'msg' => '蛋壳是空的，再接再厉吧！'];
        } elseif (9 === $drawId) {
            return ['code' => 200, 'type' => 3, 'name' => '100元京东卡'];
        } else {
            return ['code' => 400, 'msg' => '系统繁忙，请稍后重试！'];
        }
    }
}

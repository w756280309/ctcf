<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\thirdparty\SocialConnect;
use common\models\user\User;
use Yii;

class Promo171228 extends BasePromo
{
    private $maxPullCashCount = 6;
    private $rewardSn = 'R_18.8';

    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;
        $startTime = new \DateTime($this->promo->startTime);
        $endTime = new \DateTime($this->promo->endTime);

        //判断是否为活动期间注册
        $registerTime = new \DateTime(date('Y-m-d H:i:s', $user->created_at));
        if ($registerTime > $endTime || $registerTime < $startTime) {
            throw new \Exception('非活动期间注册用户');
        }

        //判断是否是首次投资
        if (!$order->isFirstInvestment()) {
            throw new \Exception('当前不是首次投资');
        }

        //获取邀请者
        $inviter = $user->fetchInviter();
        if (null === $inviter) {
            throw new \Exception('无邀请用户');
        }

        //判断邀请者是否发起了召集
        $callout = Callout::find()->where(['user_id' => $inviter->id, 'promo_id' => $this->promo->id])->one();
        if (null === $callout) {
            throw new \Exception('未发起召集，不再满足活动条件');
        }

        //判断当前受召集被邀请首投的人数是否小于6次
        $cashCount = Award::findByPromoUser($this->promo, $inviter)->count();
        if ($cashCount >= $this->maxPullCashCount) {
            throw new \Exception('邀请好友首投已达上限'.$this->maxPullCashCount.'人');
        }

        //查找要发送的奖品
        $reward = Reward::fetchOneBySn($this->rewardSn);
        if (null === $reward) {
            throw new \Exception('未找到奖品');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $promoLotteryTicket = new PromoLotteryTicket(['id' => $user->id]);
            //更新实际完成首投响应次数，最大不超过6次
            $callout->responderCount = $callout->responderCount + 1;
            $callout->save(false);
            PromoService::award($inviter, $reward, $this->promo, $promoLotteryTicket);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }
}

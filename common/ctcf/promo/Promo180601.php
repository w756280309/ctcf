<?php
namespace common\ctcf\promo;

use common\models\promo\BasePromo;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use common\models\user\User;
use common\models\user\UserInfo;

class Promo180601 extends BasePromo
{
    //奖池概率配置
    public function getAwardPool()
    {
        return [
            '180601_CTCF_P16' => '0.25',
            '180601_CTCF_P18' => '0.25',
            '180601_CTCF_P28' => '0.299',
            '180601_CTCF_RP0.66' => '0.1',
            '180601_CTCF_RP0.88' => '0.1',
            '180601_CTCF_RP5.2' => '0.001',
        ];
    }

    //获取预约抽奖记录
    public function getDrawData(User $user)
    {
        $r = Reward::tableName();
        $p = PromoLotteryTicket::tableName();
        $result = PromoLotteryTicket::find()
            ->select("$r.*,$p.*")
            ->leftJoin("$r", "$p.reward_id = $r.id")
            ->where(["$p.user_id" => $user->id])
            ->andWhere(["$p.promo_id" => $this->promo->id])
            ->andWhere(["$p.source" => 'appointment'])
            ->asArray()
            ->one();

        return $result;
    }

    //获取年化累计投资的奖池
    public function getRewardPool($base)
    {
        return [
            '180601_CTCF_RP8' => intval($base >= 1),
            '180601_CTCF_RP48' => intval($base >= 5),
            '180601_CTCF_RP168' => intval($base >= 20),
            '180601_CTCF_RP358' => intval($base >= 50),
            '180601_CTCF_RP698' => intval($base >= 100),
        ];
    }

    //用户活动期间累计年化投资发奖
    public function doAfterSuccessLoanOrder($order)
    {
        $user = $order->user;
        $start = (new \DateTime($this->promo->startTime))->format('Y-m-d');
        $end = (new \DateTime($this->promo->endTime))->format('Y-m-d');
        $annualInvest = UserInfo::calcAnnualInvest($user->id, $start, $end) / 10000;
        $rewardInfo = $this->getRewardPool($annualInvest);
        foreach ($rewardInfo as $sn => $hasReward) {
            if ($hasReward) {
                $ticket = $this->fetchOneActiveTicket($user, $sn);
                if (!$ticket) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $key = $this->promo->id . '-' . $user->id . '-' . $sn;
                        TicketToken::initNew($key)->save(false);
                        PromoLotteryTicket::initNew($user, $this->promo, $sn)->save(false);
                        $reward = Reward::fetchOneBySn($sn);
                        PromoService::award($user, $reward, $this->promo, $ticket);
                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }
    }
}
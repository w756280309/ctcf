<?php
namespace common\ctcf\promo;

use common\models\adv\ShareLog;
use common\models\promo\BasePromo;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use common\models\user\User;
use yii\db\Query;

class Promo180701 extends BasePromo
{
    /*
     * 奖池配置
     * 50元优惠券，单人单日只能领一次
     * */
    public function getAwardPool(User $user, $joinTime)
    {
        $pool = [
            '180701_CTCF_RP88' => '0',//88元现金
            '180701_CTCF_PI_MACKET' => '0',//超市卡
            '180701_CTCF_P18' => '0.2',//18积分
            '180701_CTCF_C3' => '0.25',//3元优惠券
            '180701_CTCF_C5' => '0.25',//5元优惠券
            '180701_CTCF_C20' => '0.2',//20元优惠券
            '180701_CTCF_C50' => '0.1',//50元优惠券
            '180701_CTCF_NOREWARD' => '0',//没有奖品
        ];
        $r = Reward::tableName();
        $p = PromoLotteryTicket::tableName();
        $count = (new Query())
            ->from("$p as p")
            ->innerJoin("$r as r", 'p.reward_id = r.id')
            ->where([
                'r.sn' => '180701_CTCF_C50',
                'p.user_id' => $user->id,
                'p.promo_id' => $this->promo->id,
                'date(from_unixtime(p.created_at))' => date('Y-m-d')
            ])
            ->count();
        if ($count > 0) {
            $pool['180701_CTCF_C50'] = '0';
            $pool['180701_CTCF_C3'] = '0.35';
        }

        return $pool;
    }

    /*
     * 根据抽奖状态，确定能否增加抽奖机会
     * @param $state 1 增添免费抽奖机会  3 增加分享抽奖机会
     * 如果有抽奖机会，则返回true, 没有抽奖机会，则根据需要添加抽奖机会
     * */
    public function enableAddTicket($state, User $user)
    {
        if (!in_array($state, [1,3])) {
            return false;
        }
        $times =  (int)PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere([
                'user_id' => $user->id,
                'isDrawn' => false,
                'date(from_unixtime(created_at))' => date('Y-m-d'),
            ])
            ->count();
        if ($times > 0) {
            return true;
        }
        $source = $state == 1 ? 'free' : 'share';
        $key = $this->promo->id . '-' . $user->id . '-' . date('d') . '-' . $source;
        TicketToken::initNew($key)->save(false);
        PromoLotteryTicket::initNew($user, $this->promo, $source)->save(false);

        return true;
    }

    /*
     * 获取抽奖状态
     * state  1可以得到免费抽奖  2 分享后可以得抽奖  3 已分享可以得到抽奖机会 4今日抽奖机会已用完
     * */
    public function getDrawState(User $user)
    {
        $times = $this->getDrawnCount($user, true);
        $state = 4;
        if (1 == $times) {
            $log = ShareLog::fetchByConfig($user, 'timeline', 'p180701');
            $state = null === $log ? 2 : 3;
        } elseif (0 === $times) {
            $state = 1;
        }

        return $state;
    }
}

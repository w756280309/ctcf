<?php

namespace common\service;

use Yii;
use Exception;
use common\lib\product\ProductProcessor;
use common\lib\bchelp\BcRound;
use common\models\product\OnlineProduct;
use common\models\user\User;

/**
 * Desc 主要用于计算利息相关
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class LoanService
{
    /**
     * @param type $type        1募集中，满标  2还款中
     * @param type $order_money 订单金额
     * @param type $yield_rate  年化
     * @param type $expires     期限
     */
    public function loan($type, $order_money, $yield_rate, $expires)
    {
        bcscale(14);
        $bcround = new BcRound();
        if ($type == 1) {
            $day_lv = bcdiv($yield_rate, 360);
            $lixi = bcmul(bcmul($order_money, $day_lv), $expires);

            return $bcround->bcround($lixi, 2);
        } elseif ($type == 2) {
            return 0;
        } else {
            return 0;
        }
    }

    /**
     * 计算结算时间.
     *
     * @param type $date
     * @param type $period
     *
     * @return type
     */
    public function returnDate($date = null, $period = null)
    {
        $processor = new ProductProcessor();

        return $processor->LoanTerms('d1', $date, $period);
    }

    /**
     * 修改标的状态
     *
     * @param OnlineProduct $deal
     * @param int           $state
     *                             注：
     *                             1.联动一侧 4结束（前提条件：余额为0）
     *                             2.取消-1初始90建标中91建标成功92建标失败93标的锁定94开标0投资中1还款中2已还款3	
     */
    public static function updateLoanState(OnlineProduct $deal, $state)
    {
        if (!array_key_exists($state, Yii::$app->params['deal_status'])) {
            throw new Exception('无法匹配的标的类型');
        }
        $umpLoanState = null;
        switch ($state) {
            case 1://预告期的标的要修改联动一侧标的为开标状态
                $umpLoanState = 0;
                break;
            case 2://募集中的标的要修改联动一侧标的为投资中状态
                $umpLoanState = 1;
                break;
            case 5://还款中的标的要修改联动一侧标的为还款中状态
                $umpLoanState = 2;
                break;
            case 6://已还清的标的要修改联动一侧标的为已还款状态
                $umpLoanState = 3;
                break;
        }

        if (null !== $umpLoanState) {
            $resp = Yii::$container->get('ump')->updateLoanState($deal->id, $umpLoanState);
            if (!$resp->isSuccessful()) {
                throw new Exception('联动状态修改失败');
            }
        }
        $loanval = [
            'status' => $state,
            'sort' => OnlineProduct::STATUS_FOUND === (int) $state ? OnlineProduct::SORT_FOUND : $state * 10,
        ];
        if (OnlineProduct::STATUS_FOUND === (int) $state || OnlineProduct::STATUS_FULL === (int) $state) {
            $loanval['full_time'] = time();
        }
        OnlineProduct::updateAll($loanval, ['id' => $deal->id]);
    }

    public static function convertUid($mobiles)
    {
        if (empty($mobiles)) {
            return "";
        }
        $users = User::find()->where('mobile in ('.$mobiles.')')->all();
        $uids = '';
        foreach ($users as $user) {
            $uids .= $user->id.',';
        }
        return null === $users ? "" : substr($uids, 0, strlen($uids) - 1);
    }

    /**
     * 判断用户是否可以投定向标.
     *
     * @param OnlineProduct        $loan
     * @param \common\service\User $userOrUid 非user对象时候建议传入mobile
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function isUserAllowed(OnlineProduct $loan, $userOrUid)
    {
        if (!$loan->isPrivate) { //非定向标直接返回可投
            return true;
        }
        $finduid = '';
        if ($userOrUid instanceof User) {
            $finduid = $userOrUid->mobile;
        } elseif (is_numeric($userOrUid)) {
            $finduid = $userOrUid;
        } else {
            throw new Exception('userOrUid参数异常');
        }

        return strpos($loan->getMobiles(), $finduid);
    }
}

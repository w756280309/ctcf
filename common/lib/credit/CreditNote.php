<?php

namespace common\lib\credit;

use common\models\product\OnlineProduct;
use common\models\user\User;
use common\utils\StringUtils;
use Yii;

class CreditNote
{
    /**
     * 判断某人的购买金额是否可以购买指定id的转让项目
     * 由于user表与user_account未迁移到tx系统，暂时将该功能放到这里
     *
     * @param string $id     转让记录id
     * @param string $amount 购买金额
     * @param User   $user   User对象
     *
     * @return array ['code' => 0 or 1, message => '提示信息']
     */
    public static function check($id, $amount, User $user)
    {
        if (empty($id)) {
            return ['code' => 1, 'message' => '当前转让不存在'];
        }

        //金额格式错误
        if (empty($amount)) {
            return ['code' => 1, 'message' => '金额格式错误'];
        }
        if (!preg_match('/^[0-9]+(\.[0-9]+)?$/', $amount) || preg_match('/^[0-9]+(\.)[0-9]{3,}$/', $amount)) {
            return ['code' => 1, 'message' => '金额格式错误'];
        }

        //检查是否能从tx获得转让项目信息
        $note = Yii::$container->get('txClient')->get('credit-note/detail', ['id' => $id]);
        if (null === $note) {
            return ['code' => 1, 'message' => '当前转让不存在'];
        }

        //检查转让项目是否结束(判断时间是为了防止转让时间已到但是isClosed依然为false)
        $nowTime = new \DateTime();
        $endTime = new \DateTime($note['endTime']);
        if ($nowTime >= $endTime || $note['isClosed']) {
            return ['code' => 1, 'message' => '转让已结束'];
        }

        //检查自己不能买自己的债权
        if ((int) $user->getId() === $note['user_id']) {
            return ['code' => 1, 'message' => '您不能购买自己的转让项目'];
        }

        //最小投资金额及递增金额的初始化及由分转换成元，用于前台提示
        $noteConfig = json_decode($note['config'], true);
        $minAmount = $noteConfig['min_order_amount'];
        $incrAmount = $noteConfig['incr_order_amount'];
        $minAmountYuan = StringUtils::amountFormat2(bcdiv($minAmount, 100, 2));
        $incrAmountYuan = StringUtils::amountFormat2(bcdiv($incrAmount, 100, 2));
        $restAmount = $note['amount'] - $note['tradedAmount'];

        //检查该转让的原始项目信息是否存在
        $loan = OnlineProduct::findOne($note['asset']['loan_id']);
        if (null === $loan) {
            return ['code' => 1, 'message' => '原始项目不存在'];
        }

        //余额不足
        if (bccomp($user->lendAccount->available_balance, $amount, 2) < 0) {
            return ['code' => 1, 'message' => '金额不足'];
        }

        //可投余额为0
        if ($restAmount === 0) {
            return ['code' => 1, 'message' => '当前项目不可投,可投余额为0'];
        }

        //把购买金额换成分来运算
        $amountFen = bcmul($amount, 100, 0);
        //该笔交易成功剩下的钱
        $lastAmount = bcsub($restAmount, $amountFen);

        if (bcdiv($restAmount, $minAmount) >= 2) {
            //若可投金额大于起投金额
            if (bcdiv($amountFen, $minAmount) < 1) {
                return ['code' => 1, 'message' => '投资金额小于起投金额('.$minAmountYuan.'元)'];
            } elseif (bcdiv($restAmount, $amountFen) < 1) {
                return ['code' => 1, 'message' => '投资金额大于可投余额'];
            } elseif ($incrAmount > 0) {
                if (bcmod(bcsub($amountFen, $minAmount, 0), $incrAmount) != 0 && bcsub($restAmount, $amountFen) != 0) {
                    return ['code' => 1, 'message' => $minAmountYuan.'元起投,'.$incrAmountYuan.'元递增'];
                } elseif ($lastAmount != 0 && bcdiv($lastAmount, $minAmount) < 1) {
                    return ['code' => 1, 'message' => '购买后可投余额不可低于起投金额'];
                }
            }
        } else {
            //最后一笔投满判断
            if (bcsub($restAmount, $amountFen) != 0) {
                return ['code' => 1, 'message' => '最后一笔需要投满转让'];
            }
        }

        return ['code' => 0, 'message' => '成功'];
    }
}

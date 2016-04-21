<?php
namespace common\core;

use Yii;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\order\OnlineRepaymentRecord;
use common\lib\bchelp\BcRound;
/**
 * Desc 主要用于实时读取用户资金信息
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02
 */
class UserAccountCore {

    /**
     * 账户信息
     * @param type $uid
     * @param type $type 用户类型 1投资2融资
     * @return boolean
     */
    public function getUserAccount($uid=null,$type=1){
        if(empty($uid)){
            return FALSE;
        }
        $ua = UserAccount::findOne(['uid'=>$uid]);
        if(empty($ua)){
            return FALSE;
        }
        return $ua;
    }

    /**
     * 计算累计收益
     */
    public function getTotalProfit()
    {
        $total = OnlineRepaymentRecord::find()
            ->where(['status' => [OnlineRepaymentRecord::STATUS_DID, OnlineRepaymentRecord::STATUS_BEFORE], 'uid' => $this->uid])
            ->sum('lixi');

        return empty($total) ? '0.00' : $total;
    }

     /**
     * 累计待回收资金【本金】满标或提前募集结束后待还款本金
     * @param type $uid
     */
    public function getTotalWaitMoney($ua){
        return $ua->investment_balance;
    }

    /**
     * 资产总额 = 账户余额 + 理财资产
     */
    public function getTotalFund()
    {
        bcscale(14);
        $total = bcadd(bcadd($this->available_balance, $this->freeze_balance), $this->investment_balance);
        $bcRound = new BcRound();
        return $bcRound->bcround($total, 2);
    }
}

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
     * 累计收益
     * @param type $uid
     */
    public function getTotalProfit($uid=null){
        $total = OnlineRepaymentRecord::find()->where("status=".OnlineRepaymentRecord::STATUS_DID.' or status='.OnlineRepaymentRecord::STATUS_BEFORE)
                ->andWhere(['uid'=>$uid])->sum('lixi');
        return empty($total)?'0.00':$total;
    }
    
     /**
     * 累计待回收资金【本金】满标或提前募集结束后待还款本金
     * @param type $uid
     */   
    public function getTotalWaitMoney($ua){
        return $ua->investment_balance;
    }
    
    /**
     * 资产总额=账户余额+理财资产
     * @param type $uid
     */
    public function getTotalFund($uid = null){
        $ua = $this->getUserAccount($uid);
        $account_balance = $ua->account_balance;//账户余额
        $investment_balance = $ua->investment_balance;
        $bcRound = new BcRound();
        bcscale(14);
        return $bcRound->bcround(bcadd($investment_balance, $account_balance), 2);
    }
}

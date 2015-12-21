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
     * 累计待回收资金【本金】满标状态
     * @param type $uid
     */   
    public function getTotalWaitMoney($uid=null){
        $query = (new \yii\db\Query())
                ->select('order.order_money')
                ->from(['online_order order'])
                ->innerJoin('online_product p','order.online_pid=p.id')
                ->where(['order.uid'=>$uid,'order.status'=>1,'p.status'=> [2,3]])->sum('order_money');
        return empty($query)?'0.00':$query;
    }
    
    /**
     * 资产总额=理财资产+可用余额+冻结金额
     * @param type $uid
     */
    public function getTotalFund($uid = null){
        //$licaizichan = $this->getTotalWaitMoney($uid);//理财资产
        $ua = $this->getUserAccount($uid);
        $available_balance = $ua->available_balance;//可用余额
        $freeze_balance = $ua->freeze_balance;//冻结金额
        //var_dump($licaizichan, $available_balance,$freeze_balance);
        $bcRound = new BcRound();
        bcscale(14);
        return $bcRound->bcround(bcadd($available_balance, $freeze_balance), 2);
    }
}

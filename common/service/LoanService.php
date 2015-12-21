<?php
namespace common\service;

use common\lib\product\ProductProcessor;
use common\lib\bchelp\BcRound;
/**
 * Desc 主要用于计算利息相关
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02
 */
class LoanService {
    
    /**
     * 
     * @param type $type  1募集中，满标  2还款中
     * @param type $order_money 订单金额
     * @param type $yield_rate  年化
     * @param type $expires  期限
     */
    public function loan($type,$order_money,$yield_rate,$expires){
        bcscale(14);
        $bcround = new BcRound();
        if($type==1){
            $day_lv = bcdiv($yield_rate, 360);
            $lixi = bcmul(bcmul($order_money, $day_lv), $expires);
            return $bcround->bcround($lixi, 2);
        }else if($type==2){
            return 0;
        }else{
            return 0;
        }
    }
    
    /**
     * 计算结算时间
     * @param type $date
     * @param type $period
     * @return type
     */
    public function returnDate($date=null,$period=null){
        $processor = new ProductProcessor();
        return $processor->LoanTerms('d1',$date,$period);
    }
    
}

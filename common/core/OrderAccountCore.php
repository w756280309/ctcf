<?php
namespace common\core;

use Yii;
use common\models\order\OnlineOrder;

/**
 * Desc 主要用于实时读取用户资金信息
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02
 */
class OrderAccountCore {
    
    /**
     * 获取标的可投余额
     * @param type $pro_id
     * @return type
     */
    public function getOrderBalance($pro_id=0){
        return OnlineOrder::getOrderBalance($pro_id);
    }
    
    
}

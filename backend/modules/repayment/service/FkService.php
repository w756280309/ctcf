<?php
namespace backend\modules\order\service;

use common\lib\product\ProductProcessor;
use common\lib\bchelp\BcRound;
use common\models\product\OnlineProduct;
/**
 * Desc 放款时候service
 * Created by zhy.
 * User: zhy
 * Date: 15-11-21
 * Time: 下午4:02
 */
class FkService {
    
    /**
     * 判断条件必须是管理员和项目状态必须是满标或者项目成立
     * 验证标的是否允许放款操作
     * @param type $pid
     * @return boolean
     */
    public function examinFk($pid=null,$admin_id=0){
        if(empty($admin_id)){
            return ['res'=>0,'msg'=>'管理员不能为空']; 
        }
        if(empty($pid)){
            return ['res'=>0,'msg'=>'项目无法获取']; 
        }
        $product = OnlineProduct::find()->where(['id'=>$pid])->one();
        if(empty($product)){
            return ['res'=>0,'msg'=>'项目无法获取']; 
        }
        //项目满标和项目成立
        if(!in_array($product->status, [OnlineProduct::STATUS_FULL,OnlineProduct::STATUS_FOUND])){
            return ['res'=>0,'msg'=>'项目不可放款']; 
        }
        return TRUE;
    }
    
}

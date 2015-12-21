<?php
namespace app\modules\deal\core;

use common\models\product\OnlineProduct;
use yii\data\Pagination;

/**
 * 项目逻辑代码
 *
 * @author zhy
 */
class Deal {
    
    public function getDealsCountByCond($cond=array(),$field = "id"){
        $count = OnlineProduct::find()->where($cond)->count($field);
        var_dump($count);
    }

    public function getDealsPageByCond(){
        
    }
    
}

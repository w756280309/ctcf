<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-15
 * Time: 上午10:55
 */
namespace common\models\offline;

class OfflineRepaymentPlan extends \yii\db\ActiveRecord
{
    /**
     * 计算每期应还本息.(同正式标)
     * @param $order
     */
    public static function calcBenxi(OfflineOrder $order)
    {

    }
}
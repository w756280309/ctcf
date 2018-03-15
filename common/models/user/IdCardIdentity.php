<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-3-8
 * Time: 下午3:58
 */
namespace common\models\user;

use common\models\mall\PointRecord;
use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder;
use common\utils\SecurityUtils;
use yii\db\ActiveRecord;

class IdCardIdentity extends ActiveRecord
{
    public $idCard; //身份证号

    /**
     * 判断用户是否获得过首投奖励
     * 主要是查询积分流水
     */
    public function isGetFirstAward()
    {
        //线上积分流水
        $offPointRecord = PointRecord::find()
            ->innerJoin('user', 'user.id = point_record.user_id')
            ->where([
                'user.safeIdCard' => SecurityUtils::encrypt($this->idCard),
                'point_record.ref_type' => PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1,
                'isOffline' => false,
                ])
            ->count();
        if ($offPointRecord > 0) {
            return true;
        }
        //线下积分流水
        $onPointRecord = PointRecord::find()
            ->innerJoin('offline_user', 'offline_user.id = point_record.user_id')
            ->where([
                'offline_user.idCard' => $this->idCard,
                'point_record.ref_type' => PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1,
                'isOffline' => true,
            ])
            ->count();
        if ($onPointRecord > 0) {
            return true;
        }
        //未获得过首投奖励
        return false;
    }

    /**
     * 判断当前投资为第几次成功认购
     * @param $orderTime(时间戳)
     * @return int
     */
    public function getInvestNumber($orderTime)
    {
        //线下投资
        $off_count_orders = OfflineOrder::find()->where([
            'idCard' => $this->idCard,
            'isDeleted' => false])
            ->andWhere(['<', 'created_at', $orderTime])
            ->count();
        //线上账户投资(正式标，不包括新手标和转让)
        $on_count_orders = OnlineOrder::find()
            ->innerJoin('user', 'user.id = online_order.uid')
            ->innerJoin('online_product', 'online_product.id = online_order.online_pid')
            ->where([
                'user.safeIdCard' => SecurityUtils::encrypt($this->idCard),
                'online_order.status' => 1,
                'online_product.is_xs' => false,
            ])
            ->andWhere(['<', 'online_order.created_at', $orderTime])
            ->count();
        return $off_count_orders + $on_count_orders + 1;
    }
}
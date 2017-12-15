<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-15
 * Time: 上午10:14
 */
namespace common\jobs;

use common\models\offline\OfflineLoan;
use yii\base\Object;
use yii\queue\Job;
use Yii;

/**
 * Class RepaymentJob
 * @package common\jobs
 * 用于标的确认计息生成还款计划
 * 注：暂时用于线下标的
 */
class RepaymentJob extends Object implements Job  //需要继承Object类和Job接口
{
    public $id;     //标的id

    public function execute($queue)
    {
        $loan = OfflineLoan::findOne($this->id);
        if (!is_null($loan)) {
            $orders = $loan->getSuccessOrder(); //交易成功的订单
            if (count($orders) > 0) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    self::saveRepayment($orders);    //还款计划

                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    return false;
                }
            }
        }
    }

    public static function saveRepayment($orders)
    {
        foreach ($orders as $order) {
            $amountData = self::calcBenxi($order);
        }
    }

    public static function calcBenxi($order)
    {

    }
}
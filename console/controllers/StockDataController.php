<?php

namespace console\controllers;

use common\models\product\OnlineProduct as Loan;
use common\models\order\OnlineOrder as Ord;
use Yii;
use yii\console\Controller;

/**
 * 该类用于对历史存量数据的同步修改,通常只运行一次.
 */
class StockDataController extends Controller
{
    /**
     * 同步历史理财计划数据,改为按照isLicai字段判断.
     */
    public function actionLicai()
    {
        $res = Loan::updateAll(['isLicai' => true], ['del_status' => false, 'allowUseCoupon' => false]);

        exit('更新条数: '.$res);
    }

    /**
     * 同步历史用户的累计年化投资金额.
     */
    public function actionAnnualInvestment()
    {
        $loans = Loan::find()       //筛选不是理财计划且不是新手专享并且已经计息的正常标的
            ->where([
                'is_xs' => false,
                'allowUseCoupon' => true,
                'del_status' => false,
                'is_jixi' => true,
            ])->column();

        $orders = Ord::findAll(['status' => Ord::STATUS_SUCCESS, 'online_pid' => $loans]);    //筛选成功的订单

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($orders as $order) {
                $annualInvestment = $order->annualInvestment;

                if ($annualInvestment > 0) {
                    $res = Yii::$app->db->createCommand('update user set annualInvestment = annualInvestment + '.$annualInvestment.' where id = '.$order->user->id)->execute();

                    if (!$res) {
                        throw new \Exception('更新用户累计年化投资金额失败');
                    }
                }
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            die($e->getMessage());
        }

        exit('用户累计年化投资金额更新成功!');
    }
}
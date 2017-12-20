<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-20
 * Time: 下午5:40
 */
namespace console\controllers;

use common\models\offline\OfflineLoan;
use common\models\offline\OfflineOrder;
use yii\console\Controller;

class OfflineAprController extends Controller
{
    public function actionUpdate($data, $action = null)
    {
//        $arr = [
//            'title'=> '宁富65号',
//            'content' => [
//                [
//                    'min' => 1,
//                    'max' => 5,
//                    'apr' => 7.5,
//                ],
//                [
//                    'min' => 5,
//                    'max' => 20,
//                    'apr' => 7.8,
//                ],
//                [
//                    'min' => 20,
//                    'max' => 100,
//                    'apr' => 8,
//                ],
//                [
//                    'min' => 100,
//                    'max' => null,
//                    'apr' => 8.3,
//                ],
//            ],
//        ];
//        echo json_encode($arr);die;
        //var_dump(json_decode($data));die;
        $total_loan = 0;     //标的数量
        $total_order = 0;    //订单数量
        $total_update = 0;   //更新的订单数量
        $data = json_decode($data);
        //标的
        $loans = OfflineLoan::find()->select(['id'])->where(['like', 'title', $data->title])->all();
        $total_loan = count($loans);
        if (!empty($loans)) {
            foreach ($loans as $loan) {
                foreach ($data->content as $val) {
                    $query = OfflineOrder::find()
                        ->where(['loan_id' => $loan->id, 'apr' => null]);
                    if ($val->min) {
                        $query->andWhere(['>=', 'money', $val->min]);
                    }
                    if ($val->max) {
                        $query->andWhere(['<', 'money', $val->max]);
                    }

                    $orders = $query->all();
                    $total_order += count($orders);
                    if (!empty($orders) && $action) {
                        foreach ($orders as $order) {
                            $order->apr = bcdiv($val->apr, 100, 6);
                            if ($order->save(false)) {
                                $total_update += 1;
                            }
                        }
                    }
                }
            }
        }
        $this->stdout('标的数量：'.$total_loan.'条。'.PHP_EOL.'订单数量：'.$total_order.'条。'.PHP_EOL.'更新的订单数量：'.$total_update.'条。'.PHP_EOL);
    }
}
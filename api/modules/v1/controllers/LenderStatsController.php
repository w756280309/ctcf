<?php

namespace api\modules\v1\controllers;

use common\models\user\User;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\order\OnlineOrder;

/**
 * 投资用户统计数据,包括(ID，注册时间，充值金额&次数，取现金额&次数，投资金额&次数，是否开户，是否绑卡).
 */
class LenderStatsController extends Controller
{
    public function actionExport()
    {
        $data = [ 'title' =>
            [
                'UserID',
                'SignupTime',
                'isEpay',
                'isBingCard',
                'RechargeSuccFund(yuan)',
                'RechargeSuccNum',
                'DrawSuccFund(yuan)',
                'DrawSuccNum',
                'OrderSuccFund(yuan)',
                'OrderSuccNum',
            ],
        ];

        $u = User::tableName();
        $b = UserBanks::tableName();

        $model = (new \yii\db\Query)
            ->select("$u.*, $b.id as bid")
            ->from($u)
            ->leftJoin($b, "$u.id = $b.uid")
            ->where(['type' => User::USER_TYPE_PERSONAL])
            ->all();

        if (!$model) {
            throw new \yii\web\NotFoundHttpException('No data output.');
        }

        $recharge = RechargeRecord::find()
            ->select("sum(fund) as rtotalFund, count(id) as rtotalNum, uid")
            ->where(['status' => RechargeRecord::STATUS_YES])
            ->groupBy("uid")
            ->asArray()
            ->all();

        $draw = DrawRecord::find()
            ->select("sum(money) as dtotalFund, count(id) as dtotalNum, uid")
            ->where(['status' => [DrawRecord::STATUS_SUCCESS, DrawRecord::STATUS_EXAMINED]])
            ->groupBy("uid")
            ->asArray()
            ->all();

        $order = OnlineOrder::find()
            ->select("sum(order_money) as ototalFund, count(id) as ototalNum, uid")
            ->where(['status' => OnlineOrder::STATUS_SUCCESS])
            ->groupBy("uid")
            ->asArray()
            ->all();

        foreach ($model as $key => $val) {
            $data[$key]['id'] = $val['id'];
            $data[$key]['created_at'] = date('Y-m-d H:i:s', $val['created_at']);
            $data[$key]['idcard_status'] = $val['idcard_status'];

            if (null === $val['bid']) {
                $data[$key]['bid'] = 0;
            } else {
                $data[$key]['bid'] = 1;
            }

            $data[$key]['rtotalFund'] = 0;
            $data[$key]['rtotalNum'] = 0;
            if (1 === (int) $val['idcard_status'] && $recharge) {
                foreach ($recharge as $v) {
                    if ($val['id'] == $v['uid']) {
                        $data[$key]['rtotalFund'] = $v['rtotalFund'];
                        $data[$key]['rtotalNum'] = $v['rtotalNum'];
                    }
                }
            }

            $data[$key]['dtotalFund'] = 0;
            $data[$key]['dtotalNum'] = 0;
            if (1 === (int) $val['idcard_status'] && null !== $val['bid'] && $draw) {
                foreach ($draw as $v) {
                    if ($val['id'] === $v['uid']) {
                        $data[$key]['dtotalFund'] = $v['dtotalFund'];
                        $data[$key]['dtotalNum'] = $v['dtotalNum'];
                    }
                }
            }

            $data[$key]['ototalFund'] = 0;
            $data[$key]['ototalNum'] = 0;
            if (1 === (int) $val['idcard_status'] && null !== $val['bid'] && $order) {
                foreach ($order as $v) {
                    if ($val['id'] === $v['uid']) {
                        $data[$key]['ototalFund'] = $v['ototalFund'];
                        $data[$key]['ototalNum'] = $v['ototalNum'];
                    }
                }
            }
        }

        $this->createDownfile($data);
    }

    private function createDownfile(array $data)
    {
        if (empty($data)) {
            throw new \yii\web\NotFoundHttpException('The data is null');
        }

        $record = null;
        foreach ($data as $val) {
            $record .= implode(',', $val)."\n";
        }

        if (null !== $record) {
            header('Content-Disposition: attachment; filename="statistics.csv"');
            header('Content-Length: ' .strlen($record)); // 内容的字节数
            echo $record;
        }
    }
}

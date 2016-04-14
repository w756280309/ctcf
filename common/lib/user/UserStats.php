<?php

namespace common\lib\user;

use common\models\user\User;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\order\OnlineOrder;
use common\models\user\UserAccount;

/**
 * 用户统计.
 */
class UserStats
{
    /**
     * 统计投资用户信息.
     */
    public static function collectLenderData()
    {
        $data = [ 'title' =>
            [
                '用户ID',
                '注册时间',
                '姓名',
                '联系方式',
                '身份证号',
                '是否开户(1 开户 0 未开户)',
                '是否开通免密(1 开通 0 未开通)',
                '是否绑卡(1 绑卡 0 未绑卡)',
                '账户余额(元)',
                '充值成功金额(元)',
                '充值成功次数(次)',
                '提现成功金额(元)',
                '提现成功次数(次)',
                '投资成功金额(元)',
                '投资成功次数(次)',
            ],
        ];

        $u = User::tableName();
        $b = UserBanks::tableName();
        $a = UserAccount::tableName();

        $model = (new \yii\db\Query)
            ->select("$u.*, $b.id as bid, $a.account_balance")
            ->from($u)
            ->leftJoin($b, "$u.id = $b.uid")
            ->leftJoin($a, "$u.id = $a.uid")
            ->where(["$u.type" => User::USER_TYPE_PERSONAL])
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
            $data[$key]['name'] = $val['real_name'];
            $data[$key]['mobile'] = $val['mobile']."\t";   //手机号后面加入tab键,防止excel表格打开时,显示为科学计数法
            $data[$key]['idcard'] = $val['idcard']."\t";   //身份证号后面加入tab键,防止excel表格打开时,显示为科学计数法
            $data[$key]['idcard_status'] = $val['idcard_status'];
            $data[$key]['mianmiStatus'] = $val['mianmiStatus'];

            if (null === $val['bid']) {
                $data[$key]['bid'] = 0;
            } else {
                $data[$key]['bid'] = 1;
            }

            $data[$key]['account_balance'] = $val['account_balance'];

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
            if (null !== $val['bid'] && $draw) {
                foreach ($draw as $v) {
                    if ($val['id'] === $v['uid']) {
                        $data[$key]['dtotalFund'] = $v['dtotalFund'];
                        $data[$key]['dtotalNum'] = $v['dtotalNum'];
                    }
                }
            }

            $data[$key]['ototalFund'] = 0;
            $data[$key]['ototalNum'] = 0;
            if (null !== $val['bid'] && $order) {
                foreach ($order as $v) {
                    if ($val['id'] === $v['uid']) {
                        $data[$key]['ototalFund'] = $v['ototalFund'];
                        $data[$key]['ototalNum'] = $v['ototalNum'];
                    }
                }
            }
        }

        return $data;
    }

    /**
     * 生成csv导出文件
     */
    public static function createCsvFile(array $data)
    {
        if (empty($data)) {
            throw new \yii\web\NotFoundHttpException('The data is null');
        }

        $record = null;
        foreach ($data as $val) {
            $record .= implode(',', $val)."\n";
        }

        if (null !== $record) {
            $record = iconv('utf-8', 'gb2312', $record);//转换编码

            header('Content-Disposition: attachment; filename="statistics.csv"');
            header('Content-Length: ' .strlen($record)); // 内容的字节数

            echo $record;
        }
    }
}
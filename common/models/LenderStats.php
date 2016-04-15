<?php

namespace common\models;

use Yii;
use yii\db\Migration;
use common\models\user\User;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\order\OnlineOrder;
use common\models\user\UserAccount;

/**
 * This is the model class for table "lender_stats".
 *
 * @property string $id
 * @property integer $uid
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $name
 * @property string $mobile
 * @property string $idcard
 * @property integer $idcardStatus
 * @property integer $mianmiStatus
 * @property integer $bid
 * @property string $accountBalance
 * @property string $rtotalFund
 * @property integer $rtotalNum
 * @property string $dtotalFund
 * @property integer $dtotalNum
 * @property string $ototalFund
 * @property integer $ototalNum
 */
class LenderStats extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'LenderStats';
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'idcard' => 'Idcard',
            'idcardStatus' => 'Idcard Status',
            'mianmiStatus' => 'Mianmi Status',
            'bid' => 'Bid',
            'accountBalance' => 'Account Balance',
            'rtotalFund' => 'Rtotal Fund',
            'rtotalNum' => 'Rtotal Num',
            'dtotalFund' => 'Dtotal Fund',
            'dtotalNum' => 'Dtotal Num',
            'ototalFund' => 'Ototal Fund',
            'ototalNum' => 'Ototal Num',
        ];
    }

    /**
     * 获取历史数据
     * @param $time
     * @return array
     */
    private static function getOldData($time)
    {
        $u = User::tableName();
        $b = UserBanks::tableName();
        $a = UserAccount::tableName();
        $data = [];
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
            ->andWhere(['<=', 'created_at', $time])
            ->groupBy("uid")
            ->asArray()
            ->all();

        $draw = DrawRecord::find()
            ->select("sum(money) as dtotalFund, count(id) as dtotalNum, uid")
            ->where(['status' => [DrawRecord::STATUS_SUCCESS, DrawRecord::STATUS_EXAMINED]])
            ->andWhere(['<=', 'created_at', $time])
            ->groupBy("uid")
            ->asArray()
            ->all();

        $order = OnlineOrder::find()
            ->select("sum(order_money) as ototalFund, count(id) as ototalNum, uid")
            ->where(['status' => OnlineOrder::STATUS_SUCCESS])
            ->andWhere(['<=', 'created_at', $time])
            ->groupBy("uid")
            ->asArray()
            ->all();

        foreach ($model as $key => $val) {
            $data[$key]['uid'] = $val['id'];
            $data[$key]['created_at'] = $val['created_at'];
            $data[$key]['updated_at'] = $time;
            $data[$key]['name'] = $val['real_name'];
            $data[$key]['mobile'] = $val['mobile'];
            $data[$key]['idcard'] = $val['idcard'];
            $data[$key]['idcardStatus'] = $val['idcard_status'];
            $data[$key]['mianmiStatus'] = $val['mianmiStatus'];
            if (null === $val['bid']) {
                $data[$key]['bid'] = 0;
            } else {
                $data[$key]['bid'] = 1;
            }
            $data[$key]['accountBalance'] = $val['account_balance'];
            $data[$key]['rtotalFund'] = 0;
            $data[$key]['rtotalNum'] = 0;
            if (1 === (int)$val['idcard_status'] && $recharge) {
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
     * 定时更新时获取当次数据
     * @return array
     */
    private static function getData()
    {
        //todo 获取新数据
        return [];
    }


    /**
     * 将新数据更新至统计表
     * @return bool
     */
    public static function updateData()
    {
        @set_time_limit(0);
        $time = time();
        /* $count = self::find()->count();
         if ($count > 0) {
             $data = self::getData();
         } else {
             $data = self::getOldData($time);
         }*/
        (new Migration())->truncateTable(self::tableName());
        $data = self::getOldData($time);
        Yii::$app->db->createCommand()
            ->batchInsert(self::tableName(), [
                'uid',
                'created_at',
                'updated_at',
                'name',
                'mobile',
                'idcard',
                'idcardStatus',
                'mianmiStatus',
                'bid',
                'accountBalance',
                'rtotalFund',
                'rtotalNum',
                'dtotalFund',
                'dtotalNum',
                'ototalFund',
                'ototalNum'
            ], $data)
            ->execute();
        return true;
    }

    /**
     * 获取标题
     * @return array
     */
    private static function getTitle()
    {
        return ['title' => [
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
        ]];
    }

    /**
     * 生成csv文件
     * @throws \yii\web\NotFoundHttpException
     */
    public static function createCsvFile()
    {
        $data = self::find()->select([
            'uid',
            'created_at',
            'name',
            'mobile',
            'idcard',
            'idcardStatus',
            'mianmiStatus',
            'bid',
            'accountBalance',
            'rtotalFund',
            'rtotalNum',
            'dtotalFund',
            'dtotalNum',
            'ototalFund',
            'ototalNum'
        ])->orderBy(['created_at' => SORT_ASC])->asArray()->all();
        $data = array_merge(self::getTitle(), $data);
        $record = null;
        foreach ($data as $key => $val) {
            if ('title' !== $key) {
                $val['created_at'] = date('Y-m-d H:i:s', $val['created_at']);
            }
            $record .= implode("\t" . ',', $val) . "\n";
        }
        if (null !== $record) {
            $record = iconv('utf-8', 'gb2312', $record);//转换编码
            header('Content-Disposition: attachment; filename="LenderStats_' . date('YmdHis') . '.csv"');
            header('Content-Length: ' . strlen($record)); // 内容的字节数
            echo $record;
        }
    }
}

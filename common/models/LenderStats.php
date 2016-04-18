<?php

namespace common\models;

use Yii;
use yii\db\Exception;
use common\models\user\User;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\order\OnlineOrder;
use common\models\user\UserAccount;
use yii\helpers\ArrayHelper;

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
            'userRegTime' => 'Register Time',
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
     * 获取用户统计数据
     * @param integer $time 最新指令时间
     * @param array $uids 当$uids不为空时候，获取指定用户数据；当$uids为空时候，获取全部用户数据；
     * @return array
     */
    private static function getOldData($time, array $uids = [])
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
            ->where(["$u.type" => User::USER_TYPE_PERSONAL]);
        if ($uids) {
            $model = $model->andWhere(['in', "$u.id", $uids]);
        }
        $model = $model->all();
        if (!$model) {
            return [];
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
            $data[$key]['userRegTime'] = $val['created_at'];
            $data[$key]['created_at'] = $time;
            $data[$key]['updated_at'] = time();
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
     * 从tradelog中获取最新指令的created_at 当做 上次更新时间
     * @return mixed|null
     */
    private static function getLastUpdateTime()
    {
        $model = LenderStats::find()->select('created_at')->orderBy(['created_at' => SORT_DESC])->asArray()->one();
        if ($model) {
            return $model['created_at'];
        } else {
            return null;
        }
    }

    /**
     * 增量更新
     * 根据上次更新时间（上次最新指令时间）和所有指令类型获取最新指令，根据最新指令获取对应的用户ids,清空对应的用户统计数据，统计对应用户的数据，插入对应用户数据（新插入数据的更新时间为这次指令中的最新时间）
     * @return bool
     * @throws Exception
     */
    private static function instructionUpdate()
    {
        $last_update_time = self::getLastUpdateTime();
        if (is_null($last_update_time)) {
            self::overallUpdate();
            return true;
        }
        $types = [
            //可能产生交易的指令
            'project_transfer',//4.3.3标的转账(商户平台)
            'project_tranfer_notify',//4.3.4标的交易通知(平台商户)
            'project_transfer_nopwd',//4.3.5无密标的转入(商户平台)
            //可能产生充值的指令
            'mer_recharge_person',//4.4.1	个人客户充值申请(商户平台)
            'recharge_notify',//4.4.3	充值结果通知(平台商户)
            'mer_recharge_person_nopwd',//4.4.4	个人客户无密充值(商户平台)
            //可能产生提现的指令
            'cust_withdrawals',//4.4.5	个人客户提现(商户平台)
            //可能差生绑卡的指令
            'ptp_mer_bind_card',//4.2.2绑定银行卡(商户平台)
            'mer_bind_card_apply_notify',//4.2.5绑卡换卡申请后台通知商户(平台商户)
            'mer_bind_card_notify',//4.2.6绑卡换卡结果后台通知商户(平台商户)
            //可能产生免密的指令
            'mer_register_person',//4.2.7签约免密协议(商户平台)
            'mer_bind_agreement_notify',//4.2.8签约免密协议结果通知商户(平台商户)
        ];
        //获取所有指令
        $tradeLogs = TradeLog::find()
            ->where(['in', 'txType', $types])
            ->andWhere(['>', 'created_at', $last_update_time])
            ->asArray()
            ->all();
        if (count($tradeLogs) == 0) {
            return false;
        }
        $time = max(ArrayHelper::getColumn($tradeLogs, 'created_at'));
        //获取所有统计结果可能变动的用户uid
        $uids = [];
        foreach ($tradeLogs as $v) {
            if (in_array($v['txType'], [
                'cust_withdrawals',
                'ptp_mer_bind_card',
                'mer_bind_card_apply_notify',
                'mer_bind_card_notify',
                'mer_register_person',
                'mer_bind_agreement_notify'
            ])) {
                //提现、绑卡、注册、免密 可以获取到用户uid , 没有uid信息暂不考虑
                if ($v['uid'] > 0) {
                    $uids[] = $v['uid'];
                } else {
                    continue;
                }
            } else {
                //充值、交易 指令无法获取用户 uid ,只能通过 sn 查找
                if (in_array($v['txType'], ['project_transfer', 'project_tranfer_notify', 'project_transfer_nopwd'])) {
                    //交易
                    $sn = $v['txSn'];
                    $model = OnlineOrder::find()->where(['sn' => $sn, 'status' => 1])->asArray()->one();
                    if (!$model) {
                        continue;
                    }
                    $uids[] = $model['uid'];
                } elseif (in_array($v['txType'], ['mer_recharge_person', 'recharge_notify', 'mer_recharge_person_nopwd'])) {
                    //充值
                    $sn = $v['txSn'];
                    $model = RechargeRecord::find()->where(['sn' => $sn, 'status' => 1])->asArray()->one();
                    if (!$model) {
                        continue;
                    }
                    $uids[] = $model['uid'];
                } else {
                    continue;
                }
            }
        }
        //获取这批用户的统计结果
        if (count($uids) > 0) {
            $uids = array_unique($uids);
            $data = self::getOldData($time, $uids);
            $transaction = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->db->createCommand()->delete(self::tableName(), ['in', 'uid', $uids])->execute();
                Yii::$app->db->createCommand()
                    ->batchInsert(self::tableName(), [
                        'uid',
                        'userRegTime',
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
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        return true;
    }

    /**
     * 全局更新
     * 获取所有用户的统计数据，清空统计表，将数据插入数据库s
     * @param null $time
     * @throws Exception
     */
    private static function overallUpdate($time = null)
    {
        $time = is_null($time) ? time() : $time;
        //获取所有用户所有统计数据
        $data = self::getOldData($time);
        //清空数据库
        Yii::$app->db->createCommand()->truncateTable(self::tableName())->execute();
        //将新数据插入数据库
        Yii::$app->db->createCommand()
            ->batchInsert(self::tableName(), [
                'uid',
                'userRegTime',
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
    }

    /**
     * 将新数据更新至统计表
     * 可以全局统计，也可以增量统计
     * @return bool
     */
    public static function updateData()
    {
        @set_time_limit(0);
        //全局更新
        //self::overallUpdate();

        //指令更新
        self::instructionUpdate();
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
     */
    public static function createCsvFile()
    {
        $data = self::find()->select([
            'uid',
            'userRegTime',
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
        ])->orderBy(['userRegTime' => SORT_ASC])->asArray()->all();
        $data = array_merge(self::getTitle(), $data);
        $record = null;
        foreach ($data as $key => $val) {
            if ('title' !== $key) {
                $val['userRegTime'] = date('Y-m-d H:i:s', $val['userRegTime']);
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

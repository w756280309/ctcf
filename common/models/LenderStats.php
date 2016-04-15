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
use yii\web\NotFoundHttpException;

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


    private static function getOldData()
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
            $data[$key]['uid'] = $val['id'];
            $data[$key]['created_at'] = $val['created_at'];
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


    private static function getLastUpdateTime()
    {
        $model = LenderStats::find()->select('updated_at')->orderBy(['updated_at'=>SORT_DESC])->asArray()->one();
        if($model){
            return $model['updated_at'];
        }else{
            return null;
        }
    }


    private static function instructionUpdate(){
        $last_update_time = self::getLastUpdateTime();
        if(is_null($last_update_time)){
            self::overallUpdate();
            return true;
        }
     /*   //获取充值指令
        $tradeLogs = TradeLog::find()
            ->select('txSn')
            ->where(['>','created_at',$last_update_time])
            ->andWhere(['in','txType',['mer_recharge_person','mer_recharge','recharge_notify']])
            ->asArray()
            ->all();
        if(count($tradeLogs)>0){
            foreach($tradeLogs as $v){
                $sn = $v['txSn'];
                if(!$sn){
                    continue;
                }
                $recharge_record = RechargeRecord::find()->where(['sn'=>$sn,'status'=>1])->asArray()->one();
                if(!$recharge_record){
                    continue;
                }
                $lenderStats = LenderStats::find()->where(['uid'=>$recharge_record['uid']])->one();
                if(!$lenderStats){
                    throw new NotFoundHttpException('没找到对应统计数据');
                }

            }
        }
        return true;*/
        //todo 方案1 根据上次更新时间和指令类型（充值、提现、交易、注册、身份验证、开通免密等）获取最新指令，并根据每条指令获取对应用户数据变动情况，更新对应用户数据变动情况
        //todo 方案2 根据上次更新时间和所有指令类型获取最新指令，根据最新指令获取对应的用户ids,清空对应的用户统计数据，统计对应用户的数据，插入对应用户数据

        /**
            两种方案分析，已充值为例。
         * 方案一：
         * （1）获取指令。从TradeLog表中查找所有 created_at 大于 $last_update_time 的数据
         * （2）获取充值记录。循环指令，根据指令中的 txSn 字段，从 recharge_record 表中获取数据（根据recharge_record 表的 sn字段，并且 status=1 充值成功），如果没有找到数据则 continue；
         * （3）获取数据。根据充值记录，获取 uid ，fund
         * （4）将对应用户的统计结果的 充值金额加 fund，充值次数加1，账户余额加fund
         * （*5） 新增用户、用户状态更改（绑卡、开通免密等）,需要统计对应用户的所有信息，并插入数据库
         * 方案二：
         * （1）获取指令。从TradeLog表中查找所有 created_at 大于 $last_update_time 的数据
         * （2）获取用户。如果指令中有uid，添加uid；如果没有则根据 txSn 获取用户uid
         * （3）使用获取旧数据逻辑，获取这批用户的最新统计数据
         * （4）删除数据库中对应uid数据，将获取到的数据插入数据库
         *
         * 选择方案二
         */


    }

    private static function overallUpdate(){
        //获取所有用户所有统计数据
        $data = self::getOldData();
        //清空数据库
        (new Migration())->truncateTable(self::tableName());
        //将新数据插入数据库
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
    }
    /**
     * 将新数据更新至统计表
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

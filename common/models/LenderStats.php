<?php

namespace common\models;

use Yii;
use yii\db\Migration;

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
 * @property integer $idcard_status
 * @property integer $mianmiStatus
 * @property integer $bid
 * @property string $account_balance
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
        return 'lender_stats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'created_at'], 'required'],
            [['uid', 'created_at', 'updated_at', 'idcard_status', 'mianmiStatus', 'bid', 'rtotalNum', 'dtotalNum', 'ototalNum'], 'integer'],
            [['account_balance', 'rtotalFund', 'dtotalFund', 'ototalFund'], 'number'],
            [['name', 'idcard'], 'string', 'max' => 50],
            [['mobile'], 'string', 'max' => 11]
        ];
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
            'idcard_status' => 'Idcard Status',
            'mianmiStatus' => 'Mianmi Status',
            'bid' => 'Bid',
            'account_balance' => 'Account Balance',
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
        //todo 获取到$time 为止的所有历时数据统计结果
        return [];
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
        $time = time();
        /* $count = self::find()->count();
         if ($count > 0) {
             $data = self::getData();
         } else {
             $data = self::getOldData($time);
         }*/
        (new Migration())->truncateTable(self::tableName());
        $data = self::getOldData($time);
        //todo 将数据更新至统计表
        return true;
    }

    private static function getTitle()
    {
        return [
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
        ];
    }

    public static function createCsvFile()
    {
        $data = self::find()->orderBy(['created_at' => SORT_ASC])->asArray()->all();
        $data = array_merge(self::getTitle(), $data);

        if (empty($data)) {
            throw new \yii\web\NotFoundHttpException('The data is null');
        }

        $record = null;
        foreach ($data as $val) {
            $record .= implode(',', $val) . "\n";
        }

        if (null !== $record) {
            $record = iconv('utf-8', 'gb2312', $record);//转换编码

            header('Content-Disposition: attachment; filename="statistics.csv"');
            header('Content-Length: ' . strlen($record)); // 内容的字节数

            echo $record;
        }
    }
}

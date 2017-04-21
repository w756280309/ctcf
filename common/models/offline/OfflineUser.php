<?php

namespace common\models\offline;

use yii\db\ActiveRecord;
use \Zii\Model\CoinsTrait;
use \Zii\Model\LevelTrait;

/**
 * This is the model class for table "offline_user".
 *
 * @property integer $id
 * @property integer $realName         客户姓名
 * @property integer $idCard           身份证号码
 * @property integer $mobile           客户手机号
 * @property string  $annualInvestment 用户累计年化投资额
 */
class OfflineUser extends ActiveRecord
{
    use CoinsTrait;
    use LevelTrait;

    public function rules()
    {
        return [
            [['realName', 'idCard', 'mobile'], 'required'],
            [['realName', 'idCard', 'mobile'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'realName' => '客户姓名',
            'idCard' => '身份证号码',
            'mobile' => '客户手机号',
            'point' => '用户积分',
            'annualInvestment' => '用户累计年化投资额',
        ];
    }

    /**
     * 获得线下前$limit名累计投资金额的用户信息
     *
     * @param  int    $limit 条数
     *
     * @return array
     */
    public static function getTopList($limit = 5)
    {
        $o = OfflineOrder::tableName();
        $u = self::tableName();
        return self::find()
            ->select(["$u.mobile", "sum($o.money * 10000) as totalInvest"])
            ->leftJoin($o, "$o.user_id = $u.id")
            ->where(["$o.isDeleted" => false])
            ->groupBy("$u.id")
            ->orderBy(['totalInvest' => SORT_DESC])
            ->limit($limit)
            ->asArray()
            ->all();
    }
    /**
     * 获取线下用户
     */
    public function getInvestment_balance()
    {
        $investment_balance = OfflineOrder::find()
            ->where(['user_id' => $this->id , 'isDeleted' => false])
            ->sum('money');
        return $investment_balance;
    }
}

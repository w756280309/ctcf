<?php

namespace common\models\tx;

use Yii;
use Zii\Model\ActiveRecord;

/**
 * property int      $id         主键
 * property string   $sn         流水号，最大长度20
 * property int      $userId     用户ID
 * property string   $amount     金额，最大长度14
 * property smallint $status     状态
 *                               0未处理
 *                               1已审核
 *                               2提现成功
 *                               3提现失败
 *                               4已放款
 *                               5已经处理
 *                               11提现驳回
 * property string   $createTime 创建时间
 */
class DrawRecord extends ActiveRecord
{
    const STATUS_ZERO = 0; //未处理
    const STATUS_EXAMINED = 1; //已审核
    const STATUS_SUCCESS = 2; //提现成功
    const STATUS_FAIL = 3; //提现不成功
    const STATUS_LAUNCH_BATCHPAY = 4; //已放款,此时生成批量代付批次
    const STATUS_DEAL_FINISH = 5; //已经处理
    const STATUS_DENY = 11; //提现驳回

    public static function getDb()
    {
        return Yii::$app->db;
    }

    public function getAmount()
    {
        return (string) ($this->money * 100);
    }

    public function getFee()
    {
        return '200';
    }

    public function getUser_id()
    {
        return $this->uid;
    }

    public function getStartDate()
    {
        return date('Y-m-d', $this->created_at);
    }
}

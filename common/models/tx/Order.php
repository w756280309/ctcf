<?php

namespace common\models\tx;

use common\models\user\User;
use Yii;
use Zii\Model\ActiveRecord;

class Order extends ActiveRecord
{
    //0--投标失败---1-投标成功 2.撤标 3，无效
    const STATUS_FALSE = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_CANCEL = 2;
    const STATUS_WUXIAO = 3;

    public static function getDb()
    {
        return Yii::$app->db;
    }

    public static function tableName()
    {
        return 'online_order';
    }

    public function attributes()
    {
        return [
            'id',
            'uid',
            'online_pid',
            'order_money',
            'created_at',
            'yield_rate',
            'order_time',
            'status',
            'sn',
            //'mobile',//online_order表单tx删除mobile字段
            'username',
        ];
    }

    public function getApr()
    {
        return floatval($this->yield_rate);
    }

    public function getUser_id()
    {
        return $this->uid;
    }

    public function getOrder_id()
    {
        return $this->id;
    }

    public function getLoan_id()
    {
        return $this->online_pid;
    }

    public function getAmount()
    {
        return bcmul($this->order_money, 100, 0);
    }

    public function getOrderTime()
    {
        return date('Y-m-d H:i:s', $this->created_at);
    }

    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'online_pid']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
}

<?php

namespace common\models\transfer;

use common\models\user\User;
use yii\db\ActiveRecord;
use Zii\Behavior\DateTimeBehavior;

/**
 * Class TransferTx
 */
class TransferTx extends ActiveRecord
{
    const STATUS_INIT = 0;
    const STATUS_PENDING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILURE = 3;
    const STATUS_UNKNOWN = 4;

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => DateTimeBehavior::className(),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['sn', 'status', 'userId', 'money'], 'required'],
            ['sn', 'unique'],
            [['sn', 'ref_sn'], 'string'],
            [['createTime', 'updateTime', 'lastCronCheckTime'], 'safe'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }

    public function getStatusLabel()
    {
        $label = null;

        if (self::STATUS_INIT === $this->status) {
            $label = '初始';
        } elseif (self::STATUS_PENDING === $this->status) {
            $label = '处理中';
        } elseif (self::STATUS_SUCCESS === $this->status) {
            $label = '成功';
        } elseif (self::STATUS_FAILURE === $this->status) {
            $label = '失败';
        } elseif (self::STATUS_UNKNOWN === $this->status) {
            $label = '不明';
        }

        return $label;
    }
}

<?php

namespace common\models\growth;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "retention".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $seq           //召回的次数（序列）第一次召回就是1 第二次召回就是2
 * @property integer $tactic_id     //手段ID
 * @property string $status         //状态 init|started|failed
 * @property string $startTime
 * @property string $createTime
 */
class Retention extends ActiveRecord
{
    const STATUS_INIT = 'init';
    const STATUS_FAIL = 'failed';
    const STATUS_START = 'started';
    const STATUS_CANCEL = 'canceled';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'retention';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'seq', 'status', 'createTime'], 'required'],
            [['user_id', 'seq', 'tactic_id'], 'integer'],
            [['startTime', 'createTime'], 'safe'],
            [['status'], 'string', 'max' => 255],
        ];
    }

    public static function initNew($userId, $seq, $tacticId)
    {
        return new self([
            'user_id' => $userId,
            'seq' => $seq,
            'tactic_id' => $tacticId,
            'status' => self::STATUS_INIT,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }
}

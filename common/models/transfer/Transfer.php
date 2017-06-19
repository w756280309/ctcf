<?php

namespace common\models\transfer;

use common\models\user\User;
use common\utils\TxUtils;
use yii\db\ActiveRecord;

/**
 * Class Transfer
 * @package common\models\transfer
 *
 * @property integer    $user_id
 * @property float      $amount
 * @property string     $status
 * @property string     $sn
 */
class Transfer extends ActiveRecord
{
    const STATUS_INIT = 'init';//初始状态
    const STATUS_SUCCESS = 'success';//发送成功
    const STATUS_FAIL = 'fail';//发送失败
    const STATUS_PENDING = 'pending';//处理中

    /**
     * tableName
     */
    public static function tableName()
    {
        return 'transfer';
    }

    /**
     * rules
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'metadata'], 'required'],
            ['amount', 'number'],
            [['status'], 'string', 'max' => 10],
            [['metadata'], 'string', 'max' => 500],
            [['sn'], 'string'],
        ];
    }

    /**
     * labels.
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'createTime' => '创建时间',
            'updateTime' => '更新时间',
            'amount' => '金额',
            'metadata' => '转帐原因',
            'status' => '状态',
        ];
    }

    /**
     * 初始化transfer对象
     *
     * @param User $user
     * @param array $metadata jsonencode后示例{"intention": "red_packet", "promo_id": "123"}
     * @param string $amount
     * @param string $status
     *
     * @return Transfer
     */
    public static function initNew(User $user, $amount, array $metadata = [], $status = self::STATUS_INIT)
    {
        $transfer = new self([
            'user_id' => $user->id,
            'metadata' => json_encode($metadata),
            'createTime' => date('Y-m-d H:i:s'),
            'amount' => $amount,
            'status' => $status,
            'sn'=>TxUtils::generateSn('Tr'),
        ]);

        return $transfer;
    }
}

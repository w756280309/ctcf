<?php

namespace common\models\offline;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "offline_stats".
 *
 * @property integer $id
 * @property string $tradedAmount       募集规模,单位元
 * @property string $refundedPrincipal  兑付本金,单位元
 * @property string $refundedInterest   兑付利息,单位元
 * @property string $createTime         创建时间
 * @property string $updateTime         修改时间
 */
class OfflineStats extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offline_stats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tradedAmount', 'refundedPrincipal', 'refundedInterest', 'createTime'], 'required'],
            [['tradedAmount', 'refundedPrincipal', 'refundedInterest'], 'number'],
            [['createTime', 'updateTime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tradedAmount' => '募集规模',
            'refundedPrincipal' => '兑付本金',
            'refundedInterest' => '兑付利息',
            'createTime' => '创建时间',
            'updateTime' => '修改时间',
        ];
    }
}

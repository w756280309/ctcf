<?php

namespace common\models\cfca;

use Yii;

/**
 * This is the model class for table "cfca_checkaccount".
 *
 * @property integer $id
 * @property string $sn
 * @property integer $txtype
 * @property string $txsn
 * @property string $txamount
 * @property string $paymentamount
 * @property string $institutionfee
 * @property integer $create_at
 * @property integer $updated_at
 */
class CfcaCheckaccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cfca_checkaccount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['txtype', 'create_at', 'updated_at'], 'integer'],
            [['txamount', 'paymentamount', 'institutionfee'], 'number'],
            [['sn', 'txsn'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => 'Sn',
            'txtype' => 'Txtype',
            'txsn' => 'Txsn',
            'txamount' => 'Txamount',
            'paymentamount' => 'Paymentamount',
            'institutionfee' => 'Institutionfee',
            'create_at' => 'Create At',
            'updated_at' => 'Updated At',
        ];
    }
}

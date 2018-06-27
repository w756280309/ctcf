<?php

namespace common\models\user;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Borrower extends ActiveRecord
{
    public static function tableName()
    {
        return 'borrower';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['type'], 'required'],
            [['allowDisbursement'], 'default', 'value' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'allowDisbursement' => '设置为收款方',
            'type' => '账户类型',
        ];
    }
}
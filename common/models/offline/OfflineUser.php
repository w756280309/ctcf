<?php

namespace common\models\offline;

use yii\db\ActiveRecord;
use \Zii\Model\CoinsTrait;
use \Zii\Model\LevelTrait;

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
        ];
    }
}

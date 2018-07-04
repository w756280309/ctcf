<?php

namespace common\models\user;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class Borrower
 * @package common\models\user
 *
 * @property integer $id ID
 * @property integer $userId 用户ID
 * @property boolean $allowDisbursement 允许设置收款方 0不允许1允许
 * @property integer $type       会员类型 1企业融资方 2个人融资方 3用款方 4代偿方 5担保方
 * @property integer $created_at 创建时间
 * @property integer $updated_at 更新时间
 */
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

    /**
     * 主体是否为个人
     */
    public function isPersonal()
    {
        return 2 === $this->type;
    }
}
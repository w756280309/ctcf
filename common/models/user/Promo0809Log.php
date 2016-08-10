<?php

namespace common\models\user;

/**
 * This is the model class for table "promo0809_log".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $prize_id
 * @property string $user_address
 * @property string $createdAt
 */
class Promo0809Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'prize_id', 'user_address', 'createdAt'], 'required'],
            [['user_id', 'prize_id'], 'integer'],
            [['createdAt'], 'safe'],
            [['user_address'], 'string', 'max' => 255]
        ];
    }
}

<?php

namespace common\models\promo;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_promo".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $promo_key
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserPromo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_promo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['promo_key'], 'string', 'max' => 50],
            [['user_id', 'promo_key'], 'unique', 'targetAttribute' => ['user_id', 'promo_key'], 'message' => 'The combination of User ID and Promo Key has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'promo_key' => 'Promo Key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }
}

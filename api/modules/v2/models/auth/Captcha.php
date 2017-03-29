<?php

namespace api\modules\v2\models\auth;

/**
 * 图形验证码
 * This is the model class for table "captcha".
 *
 * @property string $id             验证码ID
 * @property string $code           验证码code
 * @property string $createTime     验证码创建时间
 * @property string $expireTime     验证码过期时间 十分钟后过期
 */
class Captcha extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'captcha';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'code', 'createTime', 'expireTime'], 'required'],
            [['createTime', 'expireTime'], 'safe'],
            [['id', 'code'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '验证码ID',
            'code' => '验证码Code',
            'createTime' => '创建验证码时间',
            'expireTime' => '验证码过期时间',
        ];
    }
}

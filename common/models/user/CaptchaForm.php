<?php
namespace common\models\user;

use yii\base\Model;

/**
 * Editpass form.
 */
class CaptchaForm extends Model
{
    public $captchaCode;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['captchaCode', 'required', 'message' => '图形验证码不能为空'],
            ['captchaCode', 'captcha', 'message' => '图形验证码输入错误'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'captchaCode' => '',
        ];
    }
}

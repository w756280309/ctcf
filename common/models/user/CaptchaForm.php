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
            ['captchaCode', 'required'],
            ['captchaCode', 'captcha'],
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

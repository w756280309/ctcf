<?php

namespace common\models\fenxiao;

use common\lib\validator\LoginpassValidator;
use yii\base\Model;

class FenxiaoForm extends Model
{
    public $loginName;
    public $password;
    public $affCode;
    public $affName;
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loginName', 'affCode', 'affName'], 'required'],
            [['loginName', 'password'], 'string', 'length' => [6, 20]],
            ['loginName', 'match', 'pattern' => '/(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/', 'message' => '{attribute}必须为数字和字母的组合'],
            ['password', LoginpassValidator::className(), 'skipOnEmpty' => true],
            ['affCode', 'match', 'pattern' => '/^[0-9a-zA-Z_-]+$/', 'message' => '{attribute}格式错误，只允许字母、数字、"_"和"-"。'],
            ['imageFile', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => 10240],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'loginName' => '登录名称',
            'password' => '密码',
            'affCode' => '分销商渠道码',
            'affName' => '分销商名称',
            'imageFile' => '',
        ];
    }
}
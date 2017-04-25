<?php

namespace Xii\Crm\Model;


use yii\base\Model;
use Zii\Validator\CnMobileValidator;

class IdentityForm extends Model
{

    public $numberType;
    public $name;
    public $number;

    public function rules()
    {
        return [
            ['numberType', 'in', 'range' => [Contact::TYPE_MOBILE, Contact::TYPE_LANDLINE]],
            ['name', 'string', 'max' => '20'],
            [['name', 'number'], 'trim'],
            [['name', 'numberType', 'number'], 'required'],
            ['number', CnMobileValidator::className(), 'when' => function ($model) {
                return $model->numberType === Contact::TYPE_MOBILE;
            }],
            ['number', 'validateNumber', 'when' => function($model) {
                return $model->numberType !== Contact::TYPE_MOBILE;
            }],
            ['name', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}]{1,6}$/u'],
        ];
    }

    public function validateName($attribute, $params)
    {
        if (!preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $this->name)) {
            $this->addError($attribute, '姓名格式不正确');
        }
    }

    public function validateNumber($attribute, $params)
    {
        if($this->numberType === Contact::TYPE_LANDLINE) {
            if (preg_match('/^\d{8}$/', $this->number)) {
                $this->number = '0577-'.$this->number;
            }
            if (
                !preg_match('/^\d{3}-\d{8}$/', $this->number)
                && !preg_match('/^\d{4}-\d{8}$/', $this->number)
            ) {
                $this->addError($attribute, '座机格式不正确');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'name'  => '姓名',
            'numberType' => '号码类型',
            'number' => '号码',
            'content' => '备注',
        ];
    }

}
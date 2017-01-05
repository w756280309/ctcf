<?php

namespace Zii\Validator;

use yii\validators\Validator;

/**
 * 大陆身份证格式校验
 */
class CnIdCardValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!preg_match('/(^\d{15}$)|(^\d{17}(\d|X)$)/', $model->$attribute)) {
            $this->addError($model, $attribute, '{attribute}身份证号码不正确,必须为15位或者18位');
        }
    }
}

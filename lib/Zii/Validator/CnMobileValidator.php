<?php

namespace Zii\Validator;

use yii\validators\Validator;

/**
 * 大陆手机号码格式校验
 */
class CnMobileValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!preg_match('/^1\d{10}$/', $model->$attribute)) {
            $this->addError($model, $attribute, '手机号码格式不正确');
        }
    }
}

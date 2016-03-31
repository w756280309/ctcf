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
        if (!preg_match('/^1[34578]\d{9}$/', $model->$attribute)) {
            $model->addError($model, $attribute, '手机号码格式不正确');
        }
    }
}

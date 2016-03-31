<?php

namespace Zii\Validator;

use yii\validators\Validator;

/**
 * 大陆手机号码格式校验
 */
class CnCardNoValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!preg_match('/^[0-9]{16,19}$/', $model->$attribute)) {
            $model->addError($attribute, "你输入的银行卡号有误");
        }
    }
}

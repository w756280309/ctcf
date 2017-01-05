<?php

namespace Zii\Validator;

use yii\validators\Validator;

/**
 * 大陆银行卡号码格式校验
 */
class CnCardNoValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!preg_match('/^[0-9]{16,19}$/', $model->$attribute)) {
            $this->addError($model, $attribute, '你输入的银行卡号有误');
        }
    }
}

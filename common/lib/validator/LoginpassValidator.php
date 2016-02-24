<?php

namespace common\lib\validator;

use yii\validators\Validator;

/**
 * 验证注册密码是否符合规范(密码不能是纯数字或纯字母,可以允许特殊字符输入,至少是数字与字母组合)
 */
class LoginpassValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!(false === strpos($model->$attribute, ' ')
                && preg_match('/[a-zA-Z]/', $model->$attribute)
                && preg_match('/[0-9]/', $model->$attribute))) {
            $this->addError($model, $attribute, '请至少输入字母与数字组合');
        }
    }
}

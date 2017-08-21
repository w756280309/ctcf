<?php
namespace Zii\Validator;


use yii\validators\Validator;

class NameValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!preg_match("/^[\x{4e00}-\x{9fa5}]{1,6}$/u", $model->$attribute)) {
            $this->addError($model, $attribute, '{attribute}真实姓名不合法');
        }
    }
}
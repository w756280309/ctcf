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
        if (!$this->verifyFormat($model->$attribute)) {
            $this->addError($model, $attribute, '身份证号码不正确,必须为18位有效证件号码');
        }
        $idCard = $model->$attribute;
        $year =  substr($idCard, 6, 4);
        if ($year > date('Y', strtotime('-18 year'))) {
            $this->addError($model, $attribute, '您未满18周岁，不符合注册条件，请您谅解！');
        }
    }

    private function verifyFormat($string) {
        if (18 !== strlen($string)) {
            return false;
        }

        $weights = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $checkChars = ['1', '0', 'x', '9', '8', '7', '6', '5', '4', '3', '2'];

        $sum = 0;
        for ($i = 0; $i < 17; ++$i) {
            $sum += $weights[$i] * $string[$i];
        }

        $checksum = $checkChars[$sum % 11];

        return strtolower($string[17]) === $checksum;
    }

}

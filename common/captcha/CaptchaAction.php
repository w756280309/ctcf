<?php

namespace common\captcha;

use Gregwar\Captcha\CaptchaBuilder;
use yii\captcha\CaptchaAction as BaseAction;

class CaptchaAction extends BaseAction
{
    protected function renderImage($code)
    {
        $builder = new CaptchaBuilder($code);
        $builder->build($this->width, $this->height);
        return $builder->get();
    }
}

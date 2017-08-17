<?php

namespace common\captcha;

use Gregwar\Captcha\CaptchaBuilder;
use yii\captcha\CaptchaAction as BaseAction;
use yii\helpers\Url;
use yii\web\Response;
use Yii;

class CaptchaAction extends BaseAction
{
    protected function renderImage($code)
    {
        $builder = new CaptchaBuilder($code);
        $builder->setMaxBehindLines(1);
        $builder->setMaxFrontLines(1);
        $builder->build($this->width, $this->height);
        return $builder->get();
    }

    protected function generateVerifyCode()
    {
        if ($this->minLength > $this->maxLength) {
            $this->maxLength = $this->minLength;
        }
        if ($this->minLength < 3) {
            $this->minLength = 3;
        }
        if ($this->maxLength > 20) {
            $this->maxLength = 20;
        }
        $length = mt_rand($this->minLength, $this->maxLength);
        $code = '';
        for ($i = 0; $i < $length; ++$i) {
            $code .= mt_rand(0, 9);
        }

        return $code;
    }

    public function run()
    {
        if (Yii::$app->request->getQueryParam(self::REFRESH_GET_VAR) !== null) {
            // AJAX request for regenerating code
            $code = $this->getVerifyCode(true);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'hash1' => $this->generateValidationHash($code),
                'hash2' => $this->generateValidationHash(strtolower($code)),
                // we add a random 'v' parameter so that FireFox can refresh the image
                // when src attribute of image tag is changed
                'url' => Url::to([$this->id, 'v' => uniqid()]),
            ];
        } else {
            $this->setHttpHeaders();
            Yii::$app->response->format = Response::FORMAT_RAW;
            return $this->renderImage($this->getVerifyCode(true));
        }
    }
}

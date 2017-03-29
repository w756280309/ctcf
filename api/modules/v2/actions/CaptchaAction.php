<?php

namespace api\modules\v2\actions;

use api\modules\v2\models\auth\Captcha;
use Gregwar\Captcha\CaptchaBuilder;
use Yii;
use yii\captcha\CaptchaAction as BaseAction;
use yii\db\Exception;
use yii\web\Response;

/**
 * 获取图形验证码action.
 *
 * 调用此接口，获取图形验证码, 返回base64处理之后的图片及captchaId
 * POST方式请求  , 无参数
 *
 * 请求头
 * ```
 * Accept:application/vnd.uft.mob.legacy+json
 * ```
 *
 * 响应结果：
 * ```
 * [
 *  'captchaId' => '验证码ID',
 *  'imgData' => 'base64 编码后的图片数据',
 * ]
 * ```
 */
class CaptchaAction extends BaseAction
{
    public function run()
    {
        $captcha = new Captcha([
            'id' => Yii::$app->security->generateRandomString(32),
            'code' => $this->generateVerifyCode(),
            'createTime' => date('Y-m-d H:i:s'),
            'expireTime' => date('Y-m-d H:i:s', strtotime('+10 minute')),
        ]);
        $res = $captcha->save();
        if (!$res) {
            throw new Exception('创建图形验证码失败');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'captchaId' => $captcha->id,
            'imgData' => base64_encode($this->renderImage($captcha->code)),
        ];
    }

    protected function renderImage($code)
    {
        $builder = new CaptchaBuilder($code);
        $builder->build($this->width, $this->height);

        return $builder->get();
    }
}

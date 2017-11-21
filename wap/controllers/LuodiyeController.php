<?php

namespace app\controllers;

use common\models\user\CaptchaForm;
use common\controllers\HelpersTrait;
use common\models\user\User;

use common\service\SmsService;
use common\utils\SecurityUtils;
use wap\modules\promotion\models\PromoMobile;
use Yii;
use yii\web\Controller;

class LuodiyeController extends Controller
{
    use HelpersTrait;

    /**
     * 落地页注册页.
     */
    public function actionSignup($next = null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark=' . time());
        }
        $captcha = new CaptchaForm();

        return $this->render('luodiye', [
            'captcha' => $captcha,
            'next' => filter_var($next, FILTER_VALIDATE_URL),
        ]);
    }

    public function actionIndex()
    {
        return $this->render('invite', ['isLuodiye' => true]);
    }

    public function actionInvite()
    {
        $code = Yii::$app->request->get('code');
        if (empty($code) || null === User::find()->where(['usercode' => $code, 'status' => 1])->one()) {
            return $this->redirect('index');
        }
        Yii::$app->session->set('inviteCode', $code);

        return $this->render('invite', ['isLuodiye' => false]);
    }

    public function actionV2()
    {
        $this->layout = 'normal';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark=' . time());
        }
        $captcha = new CaptchaForm();
        $lastVerify = Yii::$app->session->get('lastVerify');

        return $this->render('v2', [
            'captcha' => $captcha,
            'phone' => isset($lastVerify['phone']) ? $lastVerify['phone'] : '',
        ]);
    }

    public function actionCloth()
    {
        $this->layout = 'normal';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark=' . time());
        }
        $captcha = new CaptchaForm();
        $lastVerify = Yii::$app->session->get('lastVerify');

        return $this->render('v2', [
            'captcha' => $captcha,
            'phone' => isset($lastVerify['phone']) ? $lastVerify['phone'] : '',
        ]);
    }

    public function actionCreateSms()
    {
        $type = Yii::$app->request->post('type');
        $phone = Yii::$app->request->post('phone');
        $captchaCode = Yii::$app->request->post('captchaCode');
        $flag = false;

        if (empty($type) || empty($phone) || empty($captchaCode)) {
            return ['code' => 1, 'message' => '发送短信参数错误'];
        }

        $lastVerify = Yii::$app->session->get('lastVerify');
        if (null !== $lastVerify && is_array($lastVerify) && $lastVerify['phone'] === $phone && $lastVerify['code'] === $captchaCode) {
            $flag = true;
        }
        if (!$flag) {
            $model = new CaptchaForm();
            $model->captchaCode = $captchaCode;

            if (!$model->validate()) {
                return ['code' => 1, 'message' => '图形验证码输入错误'];
            }

            $promoMobile = new PromoMobile();
            $promoMobile->mobile = $phone;
            $promoMobile->ip = Yii::$app->request->userIP;
            $promoMobile->createTime = date('Y-m-d H:i:s');
            $promoMobile->referralSource = Yii::$app->request->cookies->get('campagin_source');
            $promoMobile->save(false);
            Yii::$app->session->set('lastVerify', ['phone' => $phone, 'code' => $captchaCode]);
        }

        if (1 === (int) $type) {
            //使用加密后的手机号去验证是否重复
            $user = User::findOne(['safeMobile' => SecurityUtils::encrypt($phone)]);
            if (null !== $user) {
                return ['code' => 1, 'message' => '此手机号已经注册'];
            }
        }

        return SmsService::createSmscode($type, $phone);
    }
}

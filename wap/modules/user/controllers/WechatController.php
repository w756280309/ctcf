<?php

namespace app\modules\user\controllers;

use common\controllers\HelpersTrait;
use common\models\thirdparty\SocialConnect;
use common\models\user\LoginForm;
use common\models\user\User;
use common\utils\SecurityUtils;
use Yii;
use yii\web\Controller;

class WechatController extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 微信绑定页面.
     */
    public function actionBind()
    {
        $user = $this->getAuthedUser();

        return $this->render('bind', [
            'user' => $user,
        ]);
    }

    /**
     * 微信绑定处理.
     */
    public function actionDoBind()
    {
        if (!$this->fromWx()) {
            return $this->msg400('链接失效，请在微信中打开此页面');
        }

        if (!Yii::$app->session->has('resourceOwnerId')) {
            return $this->msg400('信息获取失败，请退出重试');
        }

        $loginForm = new LoginForm();
        $loginForm->setScenario('login');
        $loginForm->phone = Yii::$app->request->post('mobile');
        $loginForm->password = Yii::$app->request->post('password');

        if ($loginForm->validate()) {
            $user = User::findOne([
                'safeMobile' => SecurityUtils::encrypt($loginForm->phone),
                'type' => User::USER_TYPE_PERSONAL,
            ]);

            if (null === $user) {
                return $this->msg400('该手机号还没有注册');
            }

            if (User::STATUS_DELETED === $user->status) {
                return $this->msg400('该用户已被锁定');
            }

            if (!$user->validatePassword($loginForm->password)) {
                return $this->msg400('手机号或密码不正确');
            }

            $openId = Yii::$app->session->get('resourceOwnerId');

            try {
                SocialConnect::bind($user, $openId, SocialConnect::PROVIDER_TYPE_WECHAT);

                if (Yii::$app->user->login($user)) {
                    $user->last_login = time();
                    $user->save(false);
                }
            } catch (\Exception $e) {
                return $this->msg400($e->getMessage());
            }
        }

        if ($loginForm->getErrors()) {
            $message = $loginForm->firstErrors;

            return $this->msg400(current($message));
        }

        return [
            'code' => 0,
            'message' => '绑定成功',
        ];
    }

    /**
     * 微信绑定成功页.
     */
    public function actionBindSuccess()
    {
        return $this->render('bind_success');
    }

    /**
     * 微信解绑页面.
     */
    public function actionUnbind()
    {
        $user = $this->getAuthedUser();

        if (null !== $this->unbindValidate($user)) {
            return $this->goHome();
        }

        $openId = Yii::$app->session->get('resourceOwnerId');

        $socialConnect = SocialConnect::findOne([
            'user_id' => $user->id,
            'resourceOwner_id' => $openId,
            'provider_type' => SocialConnect::PROVIDER_TYPE_WECHAT,
        ]);

        if (null === $socialConnect) {
            return $this->goHome();
        }

        return $this->render('unbind', [
            'user' => $user,
        ]);
    }

    /**
     * 微信解绑处理.
     */
    public function actionDoUnbind()
    {
        $user = $this->getAuthedUser();
        $msg = $this->unbindValidate($user);

        if (null !== $msg) {
            return $this->msg400($msg);
        }

        try {
            $openId = Yii::$app->session->get('resourceOwnerId');

            $res = SocialConnect::unbind($user->id, $openId, SocialConnect::PROVIDER_TYPE_WECHAT);

            if (!$res) {
                throw new \Exception('解绑失败');
            }
        } catch (\Exception $e) {
            return $this->msg400($e->getMessage());
        }

        return [
            'code' => 0,
            'message' => '解绑成功',
        ];
    }

    /**
     * 微信解绑成功页.
     */
    public function actionUnbindSuccess()
    {
        return $this->render('unbind_success');
    }

    private function unbindValidate($user)
    {
        if (!$this->fromWx()) {
            return '链接失效，请在微信中打开此页面';
        }

        if (null === $user) {
            return '您当前未登录，请登录后重试';
        }

        if (!Yii::$app->session->has('resourceOwnerId')) {
            return '信息获取失败，请退出重试';
        }

        return null;
    }

    private function msg400($msg)
    {
        Yii::$app->response->statusCode = 400;

        return [
            'code' => 1,
            'message' => $msg,
        ];
    }
}

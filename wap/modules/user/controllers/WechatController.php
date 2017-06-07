<?php

namespace app\modules\user\controllers;

use common\controllers\HelpersTrait;
use common\models\thirdparty\SocialConnect;
use common\models\user\LoginForm;
use common\models\user\User;
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
        $isLogin = true;
        $user = $this->getAuthedUser();

        try {
            $this->validate($user);
        } catch (\Exception $e) {
            if (2 !== $e->getCode()) {
                return $this->msg400($e->getMessage());
            } else {
                $isLogin = false;
            }
        }

        $request = Yii::$app->request->post();

        if ($isLogin && $request['mobile'] !== $user->getMobile()) {
            return $this->msg400('当前登录手机号与输入的手机号不相符');
        }

        $loginForm = new LoginForm();
        $loginForm->setScenario('login');
        $loginForm->phone = $request['mobile'];
        $loginForm->password = $request['password'];

        if ($loginForm->validate() && $loginForm->login(User::USER_TYPE_PERSONAL)) {
            if (!$isLogin) {
                $user = $this->getAuthedUser();
            }

            $openId = Yii::$app->session->get('resourceOwnerId');

            try {
                SocialConnect::bind($user, $openId, SocialConnect::PROVIDER_TYPE_WECHAT);
            } catch (\Exception $e) {
                if (!$isLogin) {
                    Yii::$app->user->logout();
                }

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

        try {
            $this->validate($user);
        } catch (\Exception $e) {
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

        try {
            $this->validate($user);
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

    private function validate($user)
    {
        if (!$this->fromWx()) {
            throw new \Exception('链接失效，请在微信中打开此页面', 1);
        }

        if (null === $user) {
            throw new \Exception('您当前未登录，请登录后重试', 2);
        }

        if (!Yii::$app->session->has('resourceOwnerId')) {
            throw new \Exception('信息获取失败，请退出重试', 3);
        }
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

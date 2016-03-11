<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use common\service\SmsService;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\adv\Adv;
use common\models\product\OnlineProduct;
use common\models\user\SignupForm;
use common\models\user\LoginForm;
use common\models\user\EditpassForm;
use common\service\LoginService;
use common\models\log\LoginLog;
use common\models\user\User;
use common\models\user\CaptchaForm;

/**
 * Site controller.
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                'logout' => ['post'],
                ],
            ],
             'requestbehavior' => [
                'class' => 'common\components\RequestBehavior',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'common\captcha\CaptchaAction',
                'minLength' => 4,
                'maxLength' => 4,
            ],
        ];
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $exception;

            return $this->redirect('/');
        } else {
            return '';
        }
    }

    public function actionUsererror()
    {
        $this->layout = '@app/modules/order/views/layouts/buy';

        return $this->render('usererror');
    }

    /**
     * WAP端首页展示.
     */
    public function actionIndex()
    {
        $this->layout = false;
        $ac = 5;
        $adv = Adv::find()->where(['status' => 0, 'del_status' => 0])->limit($ac)->orderBy('id desc')->asArray()->all();

        $deals = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE, 'is_xs' => 1])->orderBy('sort asc, id desc')->one();
        if (!$deals) {
            throw new \yii\web\NotFoundHttpException('The production is not existed.');
        }

        return $this->render('index', ['adv' => $adv, 'deals' => $deals]);
    }

    /**
     * 用户登陆表单页.
     */
    public function actionLogin()
    {
        $this->layout = false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $model = new LoginForm();
        $from = Yii::$app->request->referrer;
        $from = Yii::$app->functions->dealurl($from);

        $is_flag = Yii::$app->request->post('is_flag');    //是否需要校验图形验证码标志位
        if ($is_flag && !is_bool($is_flag)) {
            $is_flag = true;
        }

        if ($is_flag) {
            $model->scenario = 'verifycode';
        } else {
            $model->scenario = 'login';
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->login(User::USER_TYPE_PERSONAL)) {
                $post_from = Yii::$app->request->post('from');
                if (!empty($post_from)) {
                    return ['code' => 0, 'message' => '登录成功', 'tourl' => $post_from];
                } else {
                    $url = Yii::$app->getUser()->getReturnUrl();

                    return ['code' => 0, 'message' => '登录成功', 'tourl' => $url];
                }
            }
        }

        $login = new LoginService();

        if ($model->getErrors('password')) {
            $login->logFailure(Yii::$app->request, $model->phone, LoginLog::TYPE_WAP);
        }

        $is_flag = $is_flag ? $is_flag : $login->isCaptchaRequired(Yii::$app->request, $model->phone, 10 * 60, 3);

        if ($model->getErrors()) {
            $message = $model->firstErrors;
            if ($is_flag) {
                return ['tourl' => '/site/login', 'code' => 1, 'message' => current($message)];
            }

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('login', [
                    'model' => $model,
                    'from' => $from,
                    'is_flag' => $is_flag,
        ]);
    }

    /**
     * 注销登陆状态
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * 修改登陆密码表单页.
     */
    public function actionEditpass()
    {
        $this->layout = '@app/modules/order/views/layouts/buy';
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $model = new EditpassForm();
        $model->scenario = 'edituserpass';
        if ($model->load(Yii::$app->request->post())) {
            if ($model->edituserpass()) {
                \Yii::$app->user->logout();

                return ['code' => 0, 'message' => '修改登陆密码成功,如有其他操作需重新登陆'];
            }
        }
        if ($model->getErrors()) {
            $message = $model->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('editpass', ['model' => $model]);
    }

    /**
     * 找回密码表单页.
     */
    public function actionResetpass()
    {
        $this->layout = false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->reset_flag = 1;
            if ($model->validate() && $model->resetpass()) {
                \Yii::$app->user->logout();

                return ['code' => 1, 'message' => '密码重置成功', 'tourl' => '/site/login'];
            } else {
                $message = $model->firstErrors;

                return ['code' => 1, 'message' => current($message)];
            }
        }

        $captcha = new CaptchaForm();

        return $this->render('resetpass', ['model' => $captcha]);
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * 注册表单页.
     */
    public function actionSignup()
    {
        $this->layout = false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    $user->scenario = 'login';
                    $user->last_login = time();
                    $user->save();

                    return ['code' => 1, 'message' => '注册成功', 'tourl' => '/'];
                }
            } else {
                $error = $model->firstErrors;

                return ['code' => 1, 'message' => current($error)];
            }
        }

        $captcha = new CaptchaForm();

        return $this->render('signup', ['model' => $captcha]);
    }

    /**
     * 用户协议展示.
     */
    public function actionXieyi()
    {
        $this->layout = '@app/modules/order/views/layouts/buy';

        return $this->render('xieyi');
    }

    public function actionCreatesmscode()
    {
        $type = Yii::$app->request->post('type');
        $phone = Yii::$app->request->post('phone');
        $captchaCode = Yii::$app->request->post('captchaCode');

        if (empty($type) || empty($phone) || empty($captchaCode)) {
            return ['code' => 1, 'message' => '发送短信参数错误'];
        }

        $model = new CaptchaForm();
        $model->captchaCode = $captchaCode;

        if (!$model->validate()) {
            return ['code' => 1, 'message' => '图形验证码输入错误'];
        }

        return SmsService::createSmscode($type, $phone);
    }

    /**
     * 公司介绍
     */
    public function actionCompdesc()
    {
        $this->layout = '@app/modules/order/views/layouts/buy';
        return $this->render('company_desc');
    }

    /**
     * 新手帮助
     * @return type
     */
    public function actionHelp($type=null)
    {
        $this->layout = '@app/modules/order/views/layouts/buy';

        switch ($type) {
            case 1: return $this->render('help_loginregister');
            case 2: return $this->render('help_bindcard');
            case 3: return $this->render('help_invest');
            default: return $this->render('help');
        }
    }
}

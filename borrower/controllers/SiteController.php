<?php

namespace borrower\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\user\LoginForm;
use common\models\user\EditpassForm;
use common\service\LoginService;
use common\models\log\LoginLog;
use common\models\user\User;

/**
 * Site controller.
 */
class SiteController extends Controller
{

    public $layout = 'main';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
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
                'minLength' => 4, 'maxLength' => 4,
            ],
        ];
    }

    /**
     * 首页展示.
     */
    public function actionIndex()
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        } else {
            return $this->redirect('/user/useraccount/accountcenter');
        }
    }

    /**
     * 融资用户端登陆页面.
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $login = new LoginService();

        $showCaptcha = $login->isCaptchaRequired(Yii::$app->request->post('phone'));    //是否需要校验图形验证码标志位
        $model->scenario = $showCaptcha ? 'org_verifycode' : 'org_login';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(['username' => $model->username, 'type' => User::USER_TYPE_ORG]);
            if ($model->login(User::USER_TYPE_ORG)) {
                if (empty($user->passwordLastUpdatedTime)) {   //用户第一次登录需首先重置登录密码
                    return $this->redirect('/site/editpass');
                }

                return $this->redirect('/user/useraccount/accountcenter');
            }
        }

        if ($model->getErrors('password')) {
            $login->logFailure($model->username, LoginLog::TYPE_PC);
        }

        $showCaptcha = $login->isCaptchaRequired($model->phone);

        return $this->render('login', [
            'model' => $model,
            'showCaptcha' => $showCaptcha,
        ]);
    }

    /**
     * 登陆注销
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     *  修改登陆密码表单页.
     */
    public function actionEditpass()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }

        $model = new EditpassForm();
        $model->scenario = 'edituserpass';
        if ($model->load(Yii::$app->request->post())) {
            if ($model->edituserpass()) {
                \Yii::$app->user->logout();

                return $this->redirect('/site/login');
            }
        }

        return $this->render('editpass', ['model' => $model]);
    }

    /**
     * 用户被锁定提示页面
     */
    public function actionUsererr()
    {
        return $this->render('usererr');
    }

}

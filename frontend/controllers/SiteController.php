<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\user\LoginForm;
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
        return $this->render('index');
    }

    /**
     * PC端登陆页面.
     */
    public function actionLogin($flag = null)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if ('reg' !== $flag) {
            $flag = 'login';
        }

        $model = new LoginForm();

        $is_flag = Yii::$app->request->post('is_flag');    //是否需要校验图形验证码标志位
        if ($is_flag && !is_bool($is_flag)) {
            $is_flag = true;
        }

        if ($is_flag) {
            $model->scenario = 'verifycode';
        } else {
            $model->scenario = 'login';
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->login(User::USER_TYPE_PERSONAL)) {
            return $this->goHome();
        }

        $login = new LoginService();

        if ($model->getErrors('password')) {
            $login->logFailure(Yii::$app->request, $model->phone, LoginLog::TYPE_PC);
        }

        $is_flag = $is_flag ? $is_flag : $login->isCaptchaRequired(Yii::$app->request, $model->phone, 30 * 60, 5);

        return $this->render('login', [
            'model' => $model,
            'is_flag' => $is_flag,
            'flag' => $flag,
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
     * 用户被锁定提示页面.
     */
    public function actionUsererr()
    {
        return $this->render('usererr');
    }
}

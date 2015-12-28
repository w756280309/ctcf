<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\user\LoginForm;
use common\service\LoginService;
use common\models\log\LoginLog;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                //'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                //'backColor'=>"black",
                //'foreColor' => ''
                'minLength'=>6,'maxLength'=>6
            ],
        ];
    }

    /**
     * 首页展示
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * PC端登陆页面
     */
    public function actionLogin()
    {
        $this->layout = false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        $is_flag = Yii::$app->request->post('is_flag');    //是否需要校验图形验证码标志位
        if($is_flag && !is_bool($is_flag)) {
            $is_flag = false;
        }
        
        if ($is_flag) {
            $model->scenario = 'verifycode';
        } else {
            $model->scenario = 'login';
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->login()) {
            return $this->goHome();
        }

        $login = new LoginService();
        
        if ($model->getErrors('password')) {
            $login->logFailure(Yii::$app->request, $model->phone, LoginLog::TYPE_PC);
        }

        $is_flag = $is_flag? $is_flag : $login->isCaptchaRequired(Yii::$app->request, $model->phone, 30 * 60, 5);

        return $this->render('login', [
                    'model' => $model,
                    'is_flag' => $is_flag
        ]);
      
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

}

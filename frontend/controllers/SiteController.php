<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\adv\Adv;
use common\models\product\OnlineProduct;
use common\models\news\News;
use common\controllers\HelpersTrait;
use common\models\user\LoginForm;
use common\service\LoginService;
use common\models\log\LoginLog;
use common\models\user\User;


/**
 * Site controller.
 */
class SiteController extends Controller
{
    use HelpersTrait;

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
        //轮播图展示
        $adv = Adv::find()
            ->where(['status' => 0, 'del_status' => 0, 'showOnPc' => 1])
            ->limit(5)
            ->orderBy('show_order asc, id desc')
            ->all();

        //理财公告展示
        $notice = News::find()
            ->where(['status' => News::STATUS_PUBLISH, 'category_id' => Yii::$app->params['news_cid_notice']])
            ->orderBy('news_time desc, id desc')
            ->limit(3)
            ->all();

        //媒体报道
        $media = News::find()
            ->where(['status' => News::STATUS_PUBLISH, 'category_id' => Yii::$app->params['news_cid_media']])
            ->orderBy('news_time desc, id desc')
            ->limit(3)
            ->all();

        //推荐区展示
        $loans = OnlineProduct::find()
            ->where(['isPrivate' => 0, 'del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE])
            ->andWhere('recommendTime != 0')
            ->limit(3)
            ->orderBy('recommendTime desc, sort asc, id desc')
            ->all();

        //最新资讯
        $news = News::find()
            ->where(['status' => News::STATUS_PUBLISH, 'category_id' => Yii::$app->params['news_cid_info']])
            ->orderBy('news_time desc, id desc')
            ->limit(5)
            ->all();

        return $this->render('index', ['adv' => $adv, 'loans' => $loans, 'notice' => $notice, 'media' => $media, 'news' => $news]);
    }

    /**
     * PC端登陆页面.
     */
    public function actionLogin($flag = null)
    {
        $this->layout = '@app/views/layouts/login';

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
        $this->layout = '@app/views/layouts/footer';
        return $this->render('usererr');
    }
}

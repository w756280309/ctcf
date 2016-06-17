<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Adv;
use common\models\category\ItemCategory;
use common\models\log\LoginLog;
use common\models\news\News;
use common\models\product\OnlineProduct;
use common\models\user\CaptchaForm;
use common\models\user\LoginForm;
use common\models\user\SignupForm;
use common\models\user\User;
use common\service\LoginService;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class SiteController extends Controller
{
    use HelpersTrait;

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

        $ic = ItemCategory::tableName();
        $n = News::tableName();

        //理财公告展示
        $notice = News::find()
            ->innerJoin($ic, "$ic.item_id = $n.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$ic.category_id" => Yii::$app->params['news_cid_notice']])
            ->orderBy(["$n.news_time" => 'desc', "$n.id" => 'desc'])
            ->limit(3)
            ->all();

        //媒体报道
        $media = News::find()
            ->innerJoin($ic, "$ic.item_id = $n.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$ic.category_id" => Yii::$app->params['news_cid_media']])
            ->orderBy(["$n.news_time" => 'desc', "$n.id" => 'desc'])
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
            ->innerJoin($ic, "$ic.item_id = $n.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$ic.category_id" => Yii::$app->params['news_cid_info']])
            ->orderBy(["$n.news_time" => 'desc', "$n.id" => 'desc'])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'adv' => $adv,
            'loans' => $loans,
            'notice' => $notice,
            'media' => $media,
            'news' => $news,
        ]);
    }

    /**
     * 首页榜单.
     */
    public function actionTopList()
    {
        $cache = Yii::$app->cache;
        $key = 'topList';

        if (!$cache->get($key)) {
            $rank = new RankingPromo(['startAt' => 0, 'endAt' => 9999999999]);
            $topList = $rank->getOnline();

            $cache->set($key, $topList, 600);   //缓存十分钟
        }

        $this->layout = false;

        return $this->render('top_list', ['data' => $cache->get($key)]);
    }

    /**
     * PC端登录页面.
     *
     * 判断当前登录IP短时间内是否多次输入密码错误，需要图片验证码
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $login = new LoginService();
        $requiresCaptcha = $login->isCaptchaRequired(Yii::$app->request, '', 30 * 60, 5);

        return $this->render('login', [
            'requiresCaptcha' => $requiresCaptcha,
        ]);
    }

    /**
     * 1.通过登录ip或用户名判断是否需要验证码
     * 2.若输入的密码错误，则相关信息写入login_log表，用于上述1的判断
     * 3.返回信息格式（json）
     * 参数说明: code 状态信息 0,1,2,3 (0正确1手机号错误2密码错误3图片验证码错误)
     *       requiresCaptcha 是否需要验证码
     *       message 提示信息
     *       tourl 需要跳转页面的url
     */
    public function actionDologin()
    {
        $model = new LoginForm();
        $login = new LoginService();
        $is_flag = \Yii::$app->request->post("is_flag");
        if ($is_flag) {
            $model->scenario = 'verifycode';
        } else {
            $model->scenario = 'login';
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->login(User::USER_TYPE_PERSONAL)) {
            if ('yes' == \Yii::$app->request->post('agree')) {
                setcookie("userphone", $model->phone, time()+365*86400, '/');
            } else if ('no' == \Yii::$app->request->post('agree')) {
                setcookie("userphone", "", time()-3600, "/");
            }
            $is_flag = $login->isCaptchaRequired(Yii::$app->request, $model->phone, 30 * 60, 5);
            return ['code' => 0, 'message' => '登录成功', 'tourl' => \Yii::$app->request->hostInfo, 'requiresCaptcha'=>$is_flag, 'key'=>''];
        }

        if ($model->getErrors()) {
            if ($model->getErrors('password')) {
                $login->logFailure(Yii::$app->request, $model->phone, LoginLog::TYPE_PC);
            }

            $message = $model->firstErrors;
            $key = array_keys($message)[0];
            if ('phone' === $key) {
                $code = 1;
                $message = "手机号或密码错误";
            } else if ('password' === $key) {
                $code = 2;
                $message = "手机号或密码错误";
            } else if ('verifyCode' === $key) {
                $code = 3;
                $message = current($message);
            }
            $is_flag = $login->isCaptchaRequired(Yii::$app->request, $model->phone, 30 * 60, 5);
            return ['requiresCaptcha'=> $is_flag, 'tourl'=> '', 'code' => $code, 'message' => $message];
        }
    }

    /**
     * 登录注销
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * 注册.
     */
    public function actionSignup()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        $captcha = new CaptchaForm();

        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user && Yii::$app->user->login($user)) {
                $user->scenario = 'login';
                $user->last_login = time();
                $user->save();

                return $this->redirect('/');
            }
        }

        return $this->render('signup', ['model' => $model, 'captcha' => $captcha]);
    }

    /**
     * 用户被锁定提示页面.
     */
    public function actionUsererr()
    {
        $this->layout = '@app/views/layouts/footer';
        return $this->render('usererr');
    }

    /**
     *获取登录页面
     */
    public function actionLoginForm()
    {
        $login = new LoginService();
        $requiresCaptcha = $login->isCaptchaRequired(Yii::$app->request, '', 30 * 60, 5);
        return $this->renderFile('@frontend/views/site/_login.php', [
            'requiresCaptcha' => $requiresCaptcha
        ]);
    }

    /**
     * 获取短信验证码.
     */
    public function actionCreateSms()
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

        if (1 === (int) $type) {
            $user = User::findOne(['mobile' => $phone]);
            if (null !== $user) {
                return ['code' => 1, 'message' => '此手机号已经注册'];
            }
        }

        return SmsService::createSmscode($type, $phone);
    }
}

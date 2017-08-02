<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Adv;
use common\models\category\ItemCategory;
use common\models\category\Category;
use common\models\log\LoginLog;
use common\models\news\News;
use common\models\offline\OfflineUser;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\stats\Perf;
use common\models\user\CaptchaForm;
use common\models\user\LoginForm;
use common\models\user\SignupForm;
use common\models\user\User;
use common\service\LoginService;
use common\service\SmsService;
use common\utils\SecurityUtils;
use Yii;
use yii\helpers\ArrayHelper;
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
            'reg-success' => 'common\action\user\RegSuccessAction',       //注册成功页
            'add-affiliator' => 'common\action\user\AddAffiliatorAction', //注册成功后添加分销商
        ];
    }

    /**
     * 首页展示.
     */
    public function actionIndex()
    {
        //轮播图展示
        $now = date('Y-m-d H:i:s');
        $type = Adv::TYPE_LUNBO;
        $adv = Adv::fetchHomeBanners();
        $ic = ItemCategory::tableName();
        $n = News::tableName();
        $c = Category::tableName();

        //理财公告展示
        $notice = News::find()
            ->innerJoin($ic, "$n.id = $ic.item_id")
            ->leftJoin($c, "$ic.category_id = $c.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$c.key" => "notice"])
            ->orderBy(["$n.news_time" => SORT_DESC, "$n.id" => SORT_DESC])
            ->limit(3)
            ->all();

        //媒体报道
        $media = News::find()
            ->innerJoin($ic, "$n.id = $ic.item_id")
            ->leftJoin($c, "$ic.category_id = $c.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$c.key" => "media"])
            ->orderBy(["$n.news_time" => SORT_DESC, "$n.id" => SORT_DESC])
            ->limit(2)
            ->all();
        $first_media = !empty($media) ? $media[0] : '';

        //推荐区展示
        $loans = OnlineProduct::getRecommendLoans(3, true);

        //最新资讯
        $news = News::find()
            ->innerJoin($ic, "$n.id = $ic.item_id")
            ->leftJoin($c, "$ic.category_id = $c.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$c.key" => "info"])
            ->orderBy(["$n.news_time" => SORT_DESC, "$n.id" => SORT_DESC])
            ->limit(5)
            ->all();

        //理财指南
        $licai = News::find()
            ->innerJoin($ic, "$n.id = $ic.item_id")
            ->leftJoin($c, "$ic.category_id = $c.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$c.key" => "licai"])
            ->orderBy(["$n.news_time" => SORT_DESC, "$n.id" => SORT_DESC])
            ->limit(5)
            ->all();

        //投资技巧
        $touzi = News::find()
            ->innerJoin($ic, "$n.id = $ic.item_id")
            ->leftJoin($c, "$ic.category_id = $c.id")
            ->where(["$n.status" => News::STATUS_PUBLISH, "$c.key" => "touzi"])
            ->orderBy(["$n.news_time" => SORT_DESC, "$n.id" => SORT_DESC])
            ->limit(5)
            ->all();

        //精选项目
        $jingxuan = Issuer::find()
            ->where(['allowShowOnPc' => true])
            ->orderBy(['sort' => SORT_ASC])
            ->limit(2)
            ->all();

        return $this->render('index', [
            'adv' => $adv,
            'loans' => $loans,
            'notice' => $notice,
            'media' => $media,
            'news' => $news,
            'first_media' => $first_media,
            'licai' => $licai,
            'touzi' => $touzi,
            'jingxuan' => $jingxuan,
        ]);
    }


    /**
     * 首页统计项.
     *
     * 1. 统计募集规模;
     * 2. 统计累计兑付;
     * 3. 统计兑付利息;
     *
     * 以上都是数据都是同时包含线上与线下数据的
     *
     * 4. 添加缓存机制,时间为10分钟;
     */
    public function actionStatsForIndex()
    {
        $cache = Yii::$app->db_cache;
        $key = 'index_stats';

        if (!$cache->get($key)) {
            $statsData = Perf::getStatsForIndex();

            $cache->set($key, $statsData, 600);   //缓存十分钟
        }

        return $cache->get($key);
    }

    /**
     * 首页榜单.
     */
    public function actionTopList()
    {
        $cache = Yii::$app->cache;
        $key = 'topList';

        if (!$cache->get($key)) {
            $RankOnline = User::getTopList('2016-04-19');
            $RankOffline = OfflineUser::getTopList();
            $topList = ArrayHelper::merge($RankOnline, $RankOffline);

            if (null === $topList) {
                $topList = [];
            } else {
                ArrayHelper::multisort($topList, 'totalInvest', SORT_DESC);
            }

            $cache->set($key, array_slice($topList, 0, 5), 600);   //缓存十分钟
        }

        $this->layout = false;

        return $this->render('top_list', ['data' => $cache->get($key)]);
    }

    /**
     * PC端登录页面.
     *
     * 判断当前登录IP短时间内是否多次输入密码错误，需要图片验证码
     */
    public function actionLogin($next = null)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $login = new LoginService();
        $requiresCaptcha = $login->isCaptchaRequired();

        return $this->render('login', [
            'requiresCaptcha' => $requiresCaptcha,
            'next' => filter_var($next, FILTER_VALIDATE_URL),
        ]);
    }

    //风险测评
    public function actionRisk()
    {
        return $this->renderFile('@frontend/views/site/risk.php');
    }

    public function actionSession()
    {
        return [
            'isLoggedin' => !Yii::$app->user->isGuest,
        ];
    }

    /**
     * 1.通过登录ip或用户名判断是否需要验证码
     * 2.若输入的密码错误，则相关信息写入login_log表，用于上述1的判断
     * 3.返回信息格式（json）
     * 参数说明: code 状态信息 0,1,2,3 (0正确1手机号错误2密码错误3图片验证码错误)
     *       requiresCaptcha 是否需要验证码
     *       message 提示信息
     *       tourl 需要跳转页面的url.
     */
    public function actionDologin()
    {
        $model = new LoginForm();
        $login = new LoginService();

        $showCaptcha = $login->isCaptchaRequired(Yii::$app->request->post('phone'));    //是否需要校验图形验证码标志位
        $model->scenario = $showCaptcha ? 'verifycode' : 'login';

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->login(User::USER_TYPE_PERSONAL)) {
            if ('yes' === Yii::$app->request->post('agree')) {
                setcookie('userphone', $model->phone, time() + 365 * 86400, '/');
            } elseif ('no' === Yii::$app->request->post('agree')) {
                setcookie('userphone', '', time() - 3600, '/');
            }
            $next = Yii::$app->request->post('next');

            if (filter_var($next, FILTER_VALIDATE_URL)) {
                $toUrl = $next;
            } else {
                $toUrl = \Yii::$app->request->hostInfo;
            }

            return ['code' => 0, 'message' => '登录成功', 'tourl' => $toUrl, 'key' => ''];
        }

        if ($model->getErrors()) {
            if ($model->getErrors('password') || $model->getErrors('phone')) {
                $login->logFailure($model->phone, LoginLog::TYPE_PC);
            }

            $message = $model->firstErrors;
            $key = array_keys($message)[0];
            if ('phone' === $key) {
                $code = 1;
                $message = '手机号或密码错误';
                if ($model->isUserExist()) {
                    $user= User::findOne(['safeMobile' => SecurityUtils::encrypt($model->phone)]);
                    if (null !== $user && $user->isLocked()) {
                        $message = '该用户已被锁定';
                    }
                }
            } elseif ('password' === $key) {
                $code = 2;
                $message = '手机号或密码错误';
            } elseif ('verifyCode' === $key) {
                $code = 3;
                $message = current($message);
            }
            $showCaptcha = $login->isCaptchaRequired($model->phone);

            return ['requiresCaptcha' => $showCaptcha, 'tourl' => '', 'code' => $code, 'message' => $message];
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

        $data = Yii::$app->request->post();
        if (!isset($data['regContext']) || empty($data['regContext'])) {
            $data['regContext'] = 'pc';
        }

        if ($model->load($data)) {
            $user = $model->signup(User::REG_FROM_PC, $data['regContext']);
            if ($user && Yii::$app->user->login($user)) {
                $user->scenario = 'login';
                $user->last_login = time();
                $user->save();

                return ['code' => 0, 'tourl' => '/site/reg-success'];
            } else {
                return ['code' => 1, 'error' => $model->firstErrors];
            }
        }

        return $this->render('signup', ['captcha' => $captcha]);
    }

    /**
     * 找回密码.
     */
    public function actionResetpass()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        $captcha = new CaptchaForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->reset_flag = 1;
            if ($model->resetpass()) {
                \Yii::$app->user->logout();

                return ['code' => 0, 'tourl' => '/site/login'];
            } else {
                return ['code' => 1, 'error' => $model->firstErrors];
            }
        }

        return $this->render('resetpass', ['captcha' => $captcha]);
    }

    /**
     * 注册协议.
     */
    public function actionXieyi()
    {
        return $this->render('xieyi');
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
     *获取登录页面.
     */
    public function actionLoginForm()
    {
        $login = new LoginService();
        $requiresCaptcha = $login->isCaptchaRequired();

        return $this->renderFile('@frontend/views/site/_login.php', [
            'requiresCaptcha' => $requiresCaptcha,
        ]);
    }

    /**
     * 获取短信验证码.
     */
    public function actionCreateSms()
    {
        $type = (int) Yii::$app->request->post('type');
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

        $user = User::findOne(['safeMobile' => SecurityUtils::encrypt($phone)]);
        if (1 === $type && null !== $user) {
            return ['code' => 1, 'key' => 'phone', 'message' => '该手机号码已经注册'];
        }

        if (2 === $type && null === $user) {
            return ['code' => 1, 'key' => 'phone', 'message' => '该手机号码未注册'];
        }

        return SmsService::createSmscode($type, $phone);
    }

    public function actionAppdownload()
    {
        return $this->render('appdownload');
    }
}

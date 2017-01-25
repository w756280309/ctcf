<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\mall\ThirdPartyConnect;
use common\models\offline\OfflineStats;
use common\models\payment\Repayment;
use common\models\product\LoanFinder;
use common\service\SmsService;
use common\service\LoginService;
use common\models\adv\Adv;
use common\models\adv\Share;
use common\models\affiliation\Affiliator;
use common\models\affiliation\AffiliateCampaign;
use common\models\affiliation\UserAffiliation;
use common\models\app\AccessToken;
use common\models\bank\EbankConfig;
use common\models\bank\QpayConfig;
use common\models\bank\Bank;
use common\models\log\LoginLog;
use common\models\product\OnlineProduct;
use common\models\news\News;
use common\models\user\SignupForm;
use common\models\user\LoginForm;
use common\models\user\EditpassForm;
use common\models\user\User;
use common\models\user\CaptchaForm;
use wap\modules\promotion\models\Promo160520;
use wap\modules\promotion\models\Promo160520Log;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * Site controller.
 */
class SiteController extends Controller
{
    use HelpersTrait;

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
                'logout' => ['post', 'get'],
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
        } else {
            return '';
        }
    }

    public function actionUsererror()
    {
        return $this->render('usererror');
    }

    /**
     * WAP端首页展示.
     *
     * 1. 理财专区和新手专区只显示预告期或募集中项目,没有就不显示;
     */
    public function actionIndex()
    {
        $cond = [OnlineProduct::STATUS_PRE, OnlineProduct::STATUS_NOW];

        //新手专区
        $xs = LoanFinder::queryLoans()
            ->andWhere([
                'is_xs' => true,
                'status' => $cond,
            ])
            ->orderBy([
                'xs_status' => SORT_DESC,
                'recommendTime' => SORT_DESC,
                'sort' => SORT_ASC,
                'finish_rate' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->one();

        //理财专区
        $loans = OnlineProduct::find()
            ->where([
                'isPrivate' => 0,
                'del_status' => OnlineProduct::STATUS_USE,
                'online_status' => OnlineProduct::STATUS_ONLINE,
                'is_xs' => false,
                'status' => $cond,
            ])
            ->orderBy([
                'recommendTime' => SORT_DESC,
                'sort' => SORT_ASC,
                'finish_rate' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->limit(2)
            ->all();

        //精选项目与热门活动
        $queryAdvs = Adv::find()
            ->where([
                'status' => Adv::STATUS_SHOW,
                'del_status' => Adv::DEL_STATUS_SHOW,
                'showOnPc' => 0,
            ]);

        if (defined('IN_APP')) {
            $queryAdvs->andWhere(['isDisabledInApp' => 0]);
        }

        //热门活动
        $hotActs = $queryAdvs->andWhere(['type' => Adv::TYPE_LUNBO])
            ->orderBy([
                'show_order' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->limit(5)
            ->all();

        //公告专区
        $news = News::find()
            ->where([
                'status' => News::STATUS_PUBLISH,
                'allowShowInList' => true,
            ])
            ->orderBy(['news_time' => SORT_DESC])
            ->limit(3)
            ->all();

        return $this->render('index170109', [
            'xs' => $xs,
            'loans' => $loans,
            'hotActs' => $hotActs,
            'news' => $news,
        ]);
    }

    /**
     * 查询是否是登陆/新手.
     *
     * @return int -1 表示没有登录, 0 表示新手, 大于等于1 表示投过新手标
     */
    public function actionXs()
    {
        if (\Yii::$app->user->isGuest) {
            return -1;
        }
        $user = $this->getAuthedUser();

        return $user->xsCount();
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
        $cache = Yii::$app->cache;
        $key = 'index_stats';

        if (!$cache->get($key)) {
            $totalTradeAmount = OnlineProduct::find()
                ->where([
                    'del_status' => false,
                    'online_status' => true,
                    'isTest' => false,
                ])
                ->andWhere(['>', 'status', OnlineProduct::STATUS_PRE])
                ->sum('funded_money');

            $plan = Repayment::find()
                ->where(['isRefunded' => true])
                ->select("sum(amount) as totalAmount, sum(interest) as totalInterest")
                ->asArray()
                ->one();

            $offlineStats = OfflineStats::findOne(1);

            $tradedAmount = 0;
            $refundedPrincipal = 0;
            $refundedInterest = 0;

            if (null !== $offlineStats) {
                $tradedAmount = $offlineStats->tradedAmount;
                $refundedPrincipal = $offlineStats->refundedPrincipal;
                $refundedInterest = $offlineStats->refundedInterest;
            }

            $statsData = [
                'totalTradeAmount' => bcadd($totalTradeAmount, $tradedAmount, 2),
                'totalRefundAmount' => bcadd($plan['totalAmount'], bcadd($refundedPrincipal, $refundedInterest, 2), 2),
                'totalRefundInterest' => bcadd($plan['totalInterest'], $refundedInterest, 2),
            ];

            $cache->set($key, $statsData, 600);   //缓存十分钟
        }

        return $cache->get($key);
    }

    /**
     * 用户登陆表单页.
     */
    public function actionLogin($next = null)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect('/?mark='.time());
        }

        $model = new LoginForm();

        if (empty($next) || !filter_var($next, FILTER_VALIDATE_URL)) {
            $from = Yii::$app->request->referrer;
            if (
                !Yii::$app->request->isFromOutSite()
                && in_array(parse_url($from, PHP_URL_PATH), ['/site/signup', '/site/login'])
            ) {
                $from = '/';
            }
            //如果来自外站，登录成功之后跳到首页
            if (
                !Yii::$app->request->isFromTrustSite()
                && Yii::$app->request->isFromOutSite()
            ) {
                $from = '/';
            }

        } else {
            $from = $next;
        }

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
            $post_from = Yii::$app->request->post('from');
            if ($model->login(User::USER_TYPE_PERSONAL, defined('IN_APP'))) {
                if (!empty($post_from)) {
                    $tourl = $post_from;
                } else {
                    $tourl = Yii::$app->getUser()->getReturnUrl();
                }

                if (defined('IN_APP')) {
                    $output = array();
                    $urls = parse_url($tourl);

                    if (isset($urls['query'])) {
                        parse_str($urls['query'], $output);
                    }
                    $accessToken = AccessToken::initToken($this->getAuthedUser());
                    $accessToken->save();
                    $output['token'] = $accessToken->token;
                    $output['expire'] = $accessToken->expireTime;
                    $tourl = current(explode('?', $tourl)) . '?' . http_build_query($output);
                }
                //如果是兑吧，跳转到兑吧页面
                if (in_array(parse_url($tourl, PHP_URL_HOST), [
                    'activity.m.duiba.com.cn',//兑吧活动
                    'www.duiba.com.cn',//兑吧商城
                ])) {
                    $tourl = ThirdPartyConnect::generateLoginUrl($tourl);
                }
                return [
                    'code' => 0,
                    'message' => '登录成功',
                    'tourl' => $tourl
                ];
            }
        }

        $login = new LoginService();

        if ($model->getErrors('password') || $model->getErrors('phone')) {
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

        $hmsr = Yii::$app->request->get('hmsr');
        $aff = null;

        if (!empty($hmsr)) {
            $affCam = AffiliateCampaign::findOne(['trackCode' => $hmsr]);
            if (null !== $affCam) {
                $aff = Affiliator::findOne($affCam->affiliator_id);
            }
        }

        return $this->render('login', [
            'model' => $model,
            'from' => $from,
            'is_flag' => $is_flag,
            'aff' => $aff,
        ]);
    }

    /**
     * 注销登陆状态
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->redirect('/?mark='.time());
    }

    /**
     * 修改登陆密码表单页.
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
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect('/?mark='.time());
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
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

    /**
     * 注册表单页.
     *
     * 1. next传入项为注册成功跳转链接,是经过转译的;
     */
    public function actionSignup($next = null)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect('/?mark='.time());
        }

        $next = filter_var($next, FILTER_VALIDATE_URL);

        $model = new SignupForm();
        $data = Yii::$app->request->post();
        if (!isset($data['regContext']) || empty($data['regContext'])) {
            $data['regContext'] = 'm';
        }

        if ($model->load($data) && Yii::$app->request->isAjax) {
            if ($user = $model->signup(User::REG_FROM_WAP, $data['regContext'])) {
                $promo160520log = Promo160520Log::findOne(['mobile' => $user->mobile]);
                if ($promo160520log) {
                    Promo160520::insertCoupon($user, $promo160520log->prizeId);
                }

                $isLoggedin = defined('IN_APP')
                    ? Yii::$app->user->setIdentity($user) || true
                    : Yii::$app->user->login($user);

                if ($isLoggedin) {
                    $user->scenario = 'login';
                    $user->last_login = time();
                    $user->save();

                    if (!empty($next) && !defined('IN_APP')) {
                        $tourl = $next;
                    } else {
                        $tourl = '/site/reg-success';
                    }

                    $urls = parse_url($tourl);
                    $output = array();
                    if (defined('IN_APP')) {
                        $tokens = AccessToken::initToken($user);
                        $tokens->save();
                        $output['token'] = $tokens->token;
                        $output['expire'] = $tokens->expireTime;
                        $tourl = $urls['path'].'?'.http_build_query($output);

                        if (isset($urls['query'])) {
                            $tourl .= '&'.$urls['query'];
                        }
                    }

                    return ['code' => 1, 'message' => '注册成功', 'tourl' => $tourl];
                }
            } else {
                $error = $model->firstErrors;

                return ['code' => 1, 'message' => current($error)];
            }
        }

        $captcha = new CaptchaForm();

        return $this->render('signup', [
            'model' => $captcha,
            'next' => $next,
        ]);
    }

    /**
     * 注册成功页
     */
    public function actionRegSuccess()
    {
        //如果是游客，跳转到首页
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/?mark='.time());
        }
        return $this->render('registerSucc');
    }

    /**
     * 注册成功后添加分销商
     *
     * @return bool
     */
    public function actionAddAffiliator()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        $userId = $this->getAuthedUser()->getId();
        $id = (int) Yii::$app->request->get('id');

        $affiliator = Affiliator::findOne($id);
        $userAff = UserAffiliation::findOne(['user_id' => $userId]);
        $realUserAff = null !== $userAff ? $userAff : new UserAffiliation();
        if ($id <= 0 && null !== $realUserAff) {
            return (bool) $realUserAff->findOne(['user_id' => $userId])->delete();
        }
        if (null === $affiliator) {
            return false;
        }

        $realUserAff->trackCode = AffiliateCampaign::find()->select('trackCode')->where(['affiliator_id' => $id])->scalar();
        $realUserAff->affiliator_id = $id;
        $realUserAff->user_id = $userId;

        return $realUserAff->save();

    }

    public function actionSession()
    {
        return [
            'isLoggedin' => !Yii::$app->user->isGuest,
        ];
    }

    /**
     * 用户协议展示.
     */
    public function actionXieyi()
    {
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

        if (1 === (int) $type) {
            $user = User::findOne(['mobile' => $phone]);
            if (null !== $user) {
                return ['code' => 1, 'message' => '此手机号已经注册'];
            }
        }

        return SmsService::createSmscode($type, $phone);
    }

    /**
     * 公司介绍.
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * 公司介绍h5版.
     */
    public function actionH5()
    {
        $this->layout = false;

        $share = Share::findOne(['shareKey' => 'h5']);

        return $this->render('company_desc', ['share' => $share]);
    }

    /**
     * 新手帮助.
     */
    public function actionHelp($type = null)
    {
        $page = 'help';

        if (1 === (int) $type) {
            $e = EbankConfig::tableName();
            $q = QpayConfig::tableName();
            $b = Bank::tableName();

            $ebank = (new \yii\db\Query())
                   ->select("$e.*, $b.bankName")
                   ->from($e)
                   ->leftJoin($b, "$e.bankId = $b.id")
                   ->where(["$e.typePersonal" => 1, 'isDisabled' => 0])
                   ->all();

            $qpay = (new \yii\db\Query())
                   ->select("$q.*, $b.bankName")
                   ->from($q)
                   ->leftJoin($b, "$q.bankId = $b.id")
                   ->where(['isDisabled' => 0])
                   ->all();

            return $this->render('help_operation', ['ebank' => $ebank, 'qpay' => $qpay]);
        }

        switch ($type) {
            case 2: $page = 'help_security'; break;
            case 3: $page = 'help_company'; break;
            case 4: $page = 'help_product'; break;
            default: $page = 'help';
        }

        return $this->render($page);
    }

    /**
     * 平台优势页面.
     */
    public function actionAdvantage()
    {
        return $this->render('advantage');
    }

    /**
     * 联系我们页面.
     */
    public function actionContact()
    {
        return $this->render('contact');
    }

    /**
     * 用户隐私政策页.
     */
    public function actionPrivacy()
    {
        return $this->render('privacy');
    }
}

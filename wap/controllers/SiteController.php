<?php

namespace app\controllers;

use app\modules\wechat\controllers\PushController;
use common\controllers\HelpersTrait;
use common\models\growth\AppMeta;
use common\models\mall\PointRecord;
use common\models\mall\ThirdPartyConnect;
use common\models\message\PointMessage;
use common\models\order\OnlineOrder;
use common\models\product\LoanFinder;
use common\models\stats\Perf;
use common\models\thirdparty\SocialConnect;
use common\models\user\UserInfo;
use common\service\PointsService;
use common\service\SmsService;
use common\service\LoginService;
use common\models\adv\Adv;
use common\models\adv\Share;
use common\models\affiliation\Affiliator;
use common\models\affiliation\AffiliateCampaign;
use common\models\app\AccessToken;
use common\models\bank\EbankConfig;
use common\models\bank\QpayConfig;
use common\models\bank\Bank;
use common\models\log\LoginLog;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\promo\DuoBao;
use common\models\news\News;
use common\models\user\SignupForm;
use common\models\user\LoginForm;
use common\models\user\EditpassForm;
use common\models\user\User;
use common\models\user\CaptchaForm;
use common\utils\SecurityUtils;
use EasyWeChat\Message\Text;
use Lhjx\Noty\Noty;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

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
            'reg-success' => 'common\action\user\RegSuccessAction',       //注册成功页
            'add-affiliator' => 'common\action\user\AddAffiliatorAction', //注册成功后添加分销商
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

    /**
     * 用户冻结页面.
     */
    public function actionUsererror()
    {
        return $this->render('usererror');
    }

    /**
     * WAP端首页展示.
     *
     * 1. 理财专区和新手专区只显示预告期或募集中项目,没有就不显示;
     */
/*    public function actionIndex()
    {
        $this->layout = 'normal';
        $cond = [OnlineProduct::STATUS_PRE, OnlineProduct::STATUS_NOW];

        //新手专区
        $xs = LoanFinder::queryPublicLoans()
            ->andWhere([
                'is_xs' => true,
                'status' => $cond,
            ])
            ->orderBy([
                'xs_status' => SORT_DESC,
                'recommendTime' => SORT_DESC,
                'sort' => SORT_ASC,
                'raiseDays' => SORT_DESC,
                'finish_rate' => SORT_DESC,
                'raiseSn' => SORT_DESC,
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

        //精选项目管理
        $issuers = Issuer::find()
            ->where(['isShow' => true])
            ->andWhere(['!=', 'big_pic', 'null'])
            ->andWhere(['!=', 'mid_pic', 'null'])
            ->andWhere(['!=', 'small_pic', 'null'])
            ->orderBy(['sort' => SORT_ASC])
            ->limit(3)
            ->all();

        //开屏图
        $queryKaiping = $this->advQuery()
            ->andWhere(['type' => Adv::TYPE_KAIPING])
            ->orderBy([
                'show_order' => SORT_ASC,
                'updated_at' => SORT_DESC,
            ])
            ->one();

        //热门活动
        $hotActs = Adv::fetchHomeBanners($is_m = 1);


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
            'issuers' => $issuers,
            'hotActs' => $hotActs,
            'news' => $news,
            'kaiPing' => $queryKaiping,
        ]);
    }*/

    public function actionIndex()
    {
        $this->layout = 'normal';
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $totalAssets = $user->jGMoney;
        } else {
            $totalAssets = 0;
        }
        //热门活动
        $hotActs = Adv::fetchHomeBanners($is_m = 1, $totalAssets);

        //开屏图
        $queryKaiping = $this->advQuery()
            ->andWhere(['type' => Adv::TYPE_KAIPING])
            ->orderBy([
                'show_order' => SORT_ASC,
                'updated_at' => SORT_DESC,
            ])
            ->one();

        //公告专区
        $news = News::find()
            ->where([
                'status' => News::STATUS_PUBLISH,
                'allowShowInList' => true,
            ])
            ->andWhere(['<=', 'investLeast', $totalAssets])
            ->orderBy(['news_time' => SORT_DESC])
            ->limit(3)
            ->all();
        //推荐区展示
        $loans = OnlineProduct::getRecommendLoans(3, true);

        return $this->render('index171028', [
            'hotActs' => $hotActs,
            'news' => $news,
            'kaiPing' => $queryKaiping,
            'loans' => $loans,
        ]);
    }

    private function advQuery()
    {
        $advQuery = Adv::find()
            ->where([
                'status' => Adv::STATUS_SHOW,
                'del_status' => Adv::DEL_STATUS_SHOW,
                'showOnPc' => 0,
            ]);

        if (defined('IN_APP')) {
            $advQuery->andWhere(['isDisabledInApp' => 0]);
        }

        return $advQuery;
    }

    /**
     * 查询是否是登录/新手.
     *
     * @return int -1 表示没有登录, 0 表示新手, 大于等于1 表示投过新手标
     *  "isLoggedIn" => false, // 已登录？ "isInvestor" => false, // 是投资者？如果是新手，应该是true "showPlatformStats" => false // 显示平台统计值
     */
    public function actionXs()
    {
        $json = array(
            'isLoggedIn' => true,
        );
        if (\Yii::$app->user->isGuest) {
             $json['isLoggedIn'] = false;
             return $json;
        }
        $user = $this->getAuthedUser();
        $count = $user->xsCount();
        //当返回结果是新手时
        if ($count === 0) {
            $json['isInvestor'] = false;
        }
        //当返回结果投过新手标时
        if ($count >= 0) {
            $json['isInvestor'] = true;
        }
        $investTotal = $user->info->investTotal;
        //个人投资总额大于五万时
        if ($investTotal > 50000) {
            $json['showplatformStats'] = true;
        }
        return $json;
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

    //Udesk在线客服功能，将在线客服放到一个新的页面上，避免影响m站访问速度
    public function actionUdesk()
    {
        return $this->render('udesk');
    }
    /**
     * 用户登录表单页.
     */
    public function actionLogin($next = null)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark='.time());
        }

        $isUrl = filter_var($next, FILTER_VALIDATE_URL);
        if (null !== $next && !$isUrl) {
            return $this->redirect('/?_mark='.time());
        }

        if (empty($next) || !$isUrl) {
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

        $model = new LoginForm();
        $login = new LoginService();
        $showCaptcha = $login->isCaptchaRequired(Yii::$app->request->post('phone'));    //是否需要校验图形验证码标志位
        $model->scenario = $showCaptcha ? 'verifycode' : 'login';
        if (Yii::$app->request->isAjax) {
            $model->phone = Yii::$app->request->post('phone');
            $model->password = Yii::$app->request->post('bad');
            $model->verifyCode = Yii::$app->request->post('verifyCode');
            if ($model->validate()) {
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
                        'home.m.duiba.com.cn',//兑吧首页
                        'goods.m.duiba.com.cn',//兑吧商品页面
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
            if ($model->getErrors('password') || $model->getErrors('phone')) {
                $login->logFailure($model->phone, LoginLog::TYPE_WAP);
            }

            $showCaptcha = $login->isCaptchaRequired($model->phone);
            if ($model->getErrors()) {
                $message = $model->firstErrors;
                return ['code' => 1, 'message' => current($message), 'requiresCaptcha' => $showCaptcha];
            }
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
            'showCaptcha' => $showCaptcha,
            'aff' => $aff,
        ]);
    }

    /**
     * 注销登录状态
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->redirect('/?_mark='.time());
    }

    /**
     * 修改登录密码表单页.
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

                return ['code' => 0, 'message' => '修改登录密码成功,如有其他操作需重新登录'];
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
            return $this->redirect('/?_mark='.time());
        }

        $model = new SignupForm();
        if (Yii::$app->request->post()) {
            $model->phone = Yii::$app->request->post('phone');
            $model->password = Yii::$app->request->post('bad');
            $model->sms = Yii::$app->request->post('sms');
        }
        if (Yii::$app->request->isAjax) {
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
            return $this->redirect('/?_mark='.time());
        }

        $next = filter_var($next, FILTER_VALIDATE_URL);
        $model = new SignupForm();
        $data = Yii::$app->request->post();
        if (!isset($data['regContext']) || empty($data['regContext'])) {
            $data['regContext'] = 'm';
        }
        if (!isset($data['promoId']) || empty($data['promoId'])) {
            $data['promoId'] = null;
        }

        if (Yii::$app->request->isAjax) {
            $model->phone = $data['phone'];
            $model->password = $data['father'];
            $model->sms = $data['sms'];
            if ($user = $model->signup(User::REG_FROM_WAP, $data['regContext'], $data['promoId'])) {

                $isLoggedin = defined('IN_APP')
                    ? Yii::$app->user->setIdentity($user) || true
                    : Yii::$app->user->login($user);

                if ($isLoggedin) {
                    $user->scenario = 'login';
                    $user->last_login = time();
                    $user->save();

                    if (!empty($next)) {
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

                    //如果存在duobao_mobile 则注册的用户增加抽奖机会
                    if (Yii::$app->session->hasFlash('duobao_mobile_signup')) {
                        $promo = RankingPromo::findOne(['key' => 'duo_bao_0522']);
                        $promoAtfr = new DuoBao($promo);
                        $user = $this->getAuthedUser();
                        if (SecurityUtils::decrypt($user->safeMobile) == Yii::$app->session->getFlash('duobao_mobile_signup')) {
                            $promoAtfr->addTicketForUser($user);
                        }
                    }
                    if (Yii::$app->session->has('lastVerify')) {
                        Yii::$app->session->remove('lastVerify');
                    }
                }
                /**
                 * 绑定微信号和渠道
                 * 注：微信推送可能延时到达
                 */
                if (Yii::$app->session->has('resourceOwnerId')) {
                    $openId = Yii::$app->session->get('resourceOwnerId');
                    //绑定渠道
                    PushController::bindQD($user, $openId);
                    //绑定微信
                    try {
                        SocialConnect::connect($user, $openId, SocialConnect::PROVIDER_TYPE_WECHAT);
                        $social = SocialConnect::findOne([
                            'user_id' => $user->id,
                            'provider_type' => SocialConnect::PROVIDER_TYPE_WECHAT,
                            'resourceOwner_id' => $openId,
                        ]);
                        //发积分
                        if (!is_null($social)) {
                            $pointRecord = PointRecord::findOne([
                                'ref_type' => PointRecord::TYPE_WECHAT_CONNECT,
                                'user_id' => $user->id,
                            ]);

                            if (is_null($pointRecord)) {
                                //绑定成功,发放10积分
                                $pointRecord = new PointRecord([
                                    'ref_type' => PointRecord::TYPE_WECHAT_CONNECT,
                                    'ref_id' => $social->id,
                                    'incr_points' => 10,
                                ]);

                                $res = PointsService::addUserPoints($pointRecord, false, $user);

                                if ($res) {
                                    $pointRecord = PointRecord::findOne([
                                        'ref_type' => PointRecord::TYPE_WECHAT_CONNECT,
                                        'ref_id' => $social->id,
                                        'incr_points' => 10,
                                        'user_id' => $user->id,
                                    ]);

                                    if ($pointRecord) {
                                        Noty::send(new PointMessage($pointRecord));
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        return ['code' => 1, 'message' => '注册成功', 'tourl' => $tourl];
                    }
                }

                return ['code' => 1, 'message' => '注册成功', 'tourl' => $tourl];
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

        //使用加密后的手机号去验证是否重复
        $user = User::findOne(['safeMobile' => SecurityUtils::encrypt($phone)]);
        if (null !== $user) {
            if ($user->isLocked()) {
                return ['code' => 1, 'message' => '该用户已被锁定'];
            }

            if (1 === (int) $type) {
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

    /**
     * App下载页 - 积分商城暂时无法用
     */
    public function actionAppDownload($redirect = null)
    {
        if (null === $redirect) {
            return $this->render('v2');
        }

        //不允许重定向到带有host的站点
        $host = parse_url($redirect, PHP_URL_HOST);
        if (null !== $host) {
            return $this->render('v2');
        }

        //'on'为未开启状态,'off'为关闭状态
        $isShowAppDownload = AppMeta::getValue('is_show_app_download');
        if ('on' === $isShowAppDownload) {
            if (!$this->fromWx()) {
                return $this->redirect($redirect);
            }
            return $this->render('v2');
        }

        return $this->redirect($redirect);
    }

    /**
     * APP下载提示页 - （场景：兑吧活动微信端被封）
     *
     * App端跳转到指定目标地址
     * 非App端渲染App下载提示页
     */
    public function actionRefer($redirect = null)
    {
        $disableAppRefer = AppMeta::getValue('disable_app_refer');

        if ('on' === $disableAppRefer) {
            return $this->redirect($redirect);
        }

        if (defined('IN_APP')) {
            return $this->redirect($redirect);
        }

        return $this->render('refer');
    }
}

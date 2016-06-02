<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use common\service\SmsService;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\controllers\HelpersTrait;
use common\models\adv\Adv;
use common\models\affiliation\Affiliator;
use common\models\affiliation\AffiliateCampaign;
use common\models\product\OnlineProduct;
use common\models\user\SignupForm;
use common\models\user\LoginForm;
use common\models\user\EditpassForm;
use common\service\LoginService;
use common\models\log\LoginLog;
use common\models\user\User;
use common\models\user\CaptchaForm;
use common\models\app\AccessToken;
use common\models\bank\EbankConfig;
use common\models\bank\QpayConfig;
use common\models\bank\Bank;
use common\models\news\News;
use wap\modules\promotion\models\Promo160520;
use wap\modules\promotion\models\Promo160520Log;

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

            return $this->redirect('/');
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
     */
    public function actionIndex()
    {
        $this->layout = false;
        $ac = 5;
        $record = Adv::find()->where(['status' => 0, 'del_status' => 0, 'showOnPc' => 0]);
        if (defined('IN_APP')) {   //App端isDisabledInApp为1时,不显示轮播图
            $record->andWhere(['isDisabledInApp' => 0]);
        }

        $adv = $record->limit($ac)->orderBy('show_order asc, id desc')->all();  //修改轮播图显示顺序,先按照show_order升序排列,后按照id降序排列

        $deals = OnlineProduct::find()->where(['isPrivate' => 0, 'del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE])
            ->andWhere('recommendTime != 0')
            ->orderBy('recommendTime asc, sort asc, id desc')
            ->all();

        $deal = null;
        if (!empty($deals)) {    //当没有推荐标的时,返回null
            $num = 0;
            $key = 0;
            foreach ($deals as $k => $val) {
                ++$num;
                if ($val['status'] < 3) {
                    $deal = $val;
                } else {
                    $key = $k;
                }
            }

            if (1 === $num) {
                $deal = $deals[0];
            }

            if (empty($deal)) {
                $deal = $deals[$key];
            }
        }

        $news = News::find()
            ->where(['status' => News::STATUS_PUBLISH])
            ->orderBy('news_time desc')
            ->limit(3)
            ->all();

        return $this->render('index', ['adv' => $adv, 'deal' => $deal, 'news' => $news]);
    }

    /**
     * 用户登陆表单页.
     */
    public function actionLogin($next = null)
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if (empty($next) || !filter_var($next, FILTER_VALIDATE_URL)) {
            $from = Yii::$app->functions->dealurl(Yii::$app->request->referrer);
            if (in_array($from, ['/site/signup', '/site/login'])) {
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

                $output = array();
                $urls = parse_url($tourl);

                if (isset($urls['query'])) {
                    parse_str($urls['query'], $output);
                }
                if (defined('IN_APP')) {
                    $accessToken = AccessToken::initToken($this->getAuthedUser());
                    $accessToken->save();
                    $output['token'] = $accessToken->token;
                    $output['expire'] = $accessToken->expireTime;
                }

                return ['code' => 0, 'message' => '登录成功', 'tourl' => $urls['path'].'?'.http_build_query($output)];
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

        return $this->goHome();
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
            return $this->goHome();
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
     */
    public function actionSignup()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            if ($user = $model->signup()) {
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

                    $tourl = '/';
                    $urls = parse_url($tourl);
                    $output = array();
                    if (defined('IN_APP')) {
                        $tokens = AccessToken::initToken($user);
                        $tokens->save();
                        $output['token'] = $tokens->token;
                        $output['expire'] = $tokens->expireTime;
                        $tourl = $urls['path'].'?'.http_build_query($output);
                    }

                    return ['code' => 1, 'message' => '注册成功', 'tourl' => $tourl];
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
        return $this->render('company_desc');
    }

    /**
     * 新手帮助.
     *
     * @return type
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

    public function actionSession()
    {
        return [
            'isLoggedin' => !Yii::$app->user->isGuest,
        ];
    }

    /**
     * 用户隐私政策页.
     */
    public function actionPrivacy()
    {
        return $this->render('privacy');
    }
}

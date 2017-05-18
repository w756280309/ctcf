<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\Promo170520;
use common\models\promo\Promo201705;
use common\models\promo\PromoLotteryTicket;
use common\models\user\CaptchaForm;
use common\models\user\User;
use common\models\promo\DuoBao;
use common\service\BankService;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use wap\modules\promotion\models\PromoMobile;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class P1705Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 5月活动总览
     */
    public function actionMay($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }
        $xunzhang = 0;//确认
        if (!Yii::$app->user->isGuest) {
            $user = User::findOne(Yii::$app->user->id);
            $promo = RankingPromo::findOne(['key' => 'promo_201705']);
            $promo201705 = new Promo201705($promo);
            $xunzhang = $promo201705->getRestTicketCount($user);
        }

        return $this->render('may', [
            'share' => $share,
            'xunzhang' => $xunzhang,
        ]);
    }

    /**
     * 母亲节活动
     */
    public function actionMotherDay($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('mother_day', [
            'share' => $share,
        ]);
    }

    /**
     * 5.20周年庆活动
     */
    public function action520Day($wx_share_key = null)
    {
        $share = null;
        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170520']);
        $user = $this->getAuthedUser();
        $promoStatus = null;
        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }
        $tickets = 0;
        $drawList = [];
        $promoClass = new Promo170520($promo);
        if ($user) {
            $tickets = $promoClass->getRestTicketCount($user);
            $drawList = $promoClass->getRewardedList($user);
        }

        return $this->render('520_day', [
            'share' => $share,
            'tickets' => $tickets,
            'drawList' => $drawList,
            'user' => $user,
            'promoStatus' => $promoStatus,
        ]);
    }

    /**
     * 5.20活动抽奖.
     */
    public function actionDraw520()
    {
        $user = $this->getAuthedUser();
        if (null === $user) {
            $url = urlencode(Yii::$app->request->hostInfo.'/promotion/p1705/520-day');

            return $this->redirect('/site/login?next='.$url);
        }
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170520']);
        $promoClass = new Promo170520($promo);
        $back = null;
        try {
            $draw = $promoClass->draw($user);
            $back = [
                'code' => 0,
                'drawAmount' => StringUtils::amountFormat2($draw->reward->ref_amount),
            ];
        } catch (\Exception $e) {
            Yii::trace('520活动抽奖失败, 失败原因:'.$e->getMessage().', 用户: '.$user->id);
            Yii::$app->response->statusCode = 400;
            $back = [
                'code' => 1,
                'message' => $e->getMessage(),
            ];
        }

        return $back;
    }

    /**
     * 五一活动
     */
    public function actionMayDay($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('mayday', [
            'share' => $share,
        ]);
    }

    /**
     * 五四活动.
     */
    public function actionYouthDay($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('youth_day', [
            'share' => $share,
        ]);
    }

    /**
     * 周五上线活动 0元夺宝
     */
    public function actionDuobao($wx_share_key = null)
    {
        $share = null;
        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        //活动总人数
        $numall = DuoBao::TOTAL_JOINER_COUNT;
        $user = null;
        $isJoinWith = null;
        $joinTicket = null;
        $source = null;
        $isBind = null;//新用户帮卡为1；否则为null 用于提示弹窗
        $isZJ = null; //已登录用户浙江手机号 已登录用户且手机号为非浙江，直接触发弹窗信息

        $promo = RankingPromo::findOne(['key' => 'duobao0504']);
        $promoAtfr = new DuoBao($promo);

        //获取参与人数
        $num = $promoAtfr->totalTicketCount();

        //计算参与进度  如果总人数不为0 岁计算参与进度 例如 97 页面显示结果97%
        $jindu = $num == $numall ? 100 : min(ceil($num * 100 / $numall) , 99);

        //获取最近参加的10个用户记录 如果记录<10 则null
        $promoLotteryQuery = [];
        if ($num >= 10) {
            $p = PromoLotteryTicket::tableName();
            $u = User::tableName();
            $promoLotteryQuery = PromoLotteryTicket::find()
                ->leftJoin('user', "$p.user_id = $u.id")
                ->where(['promo_id' => $promo->id])
                ->limit(10)
                ->orderBy('created_at desc')
                ->all();
        }

        //判断活动时间,1未开始,2活动中,3已结束
        $promoTime = $promoAtfr->promoTime();

        if (!Yii::$app->user->isGuest) {
            $user = $this->getAuthedUser();
            //获取用户类别
            $source = $promoAtfr->source($user);
            //用户是否参与活动
            $isJoinWith = $promoAtfr->isJoinWith($user);
            $joinTicket = PromoLotteryTicket::findOne([
                'user_id' => $user->id,
                'promo_id' => $promo->id,
            ]);

            //判断手机号是否为浙江号码
            $isZJ = $promoAtfr->isZhejiangMobile(SecurityUtils::decrypt($user->safeMobile));//调用左队长的方法

            //校验新老用户是否绑卡
            $cond = 0 | BankService::BINDBANK_VALIDATE_N;
            $backService = BankService::check($user, $cond);
            $isBind = true;

            if ($backService['code']) {
                $isBind = false;
            }

            //点击参与活动按钮 ajax
            if (Yii::$app->request->isAjax) {
                if (1 === $promoTime) {
                    $code = 1;
                    $message = '活动未开始';
                } elseif (3 === $promoTime) {
                    $code = 2;
                    $message = '活动已结束';
                } elseif (!$isZJ) {
                    $code = 3;
                    $message = '手机号不是浙江地区的';
                } elseif ('new_user' === $source) {
                    if (!$isJoinWith) {
                        $promoAtfr->addTicketForUser($user);
                    }

                    $code = 0;
                    $message = '给活动期间注册的新用户发放抽奖记录';
                } elseif ('inviter' === $source) {
                    if (!$isJoinWith) {
                        $promoAtfr->addTicketForUser($user);
                    }

                    $code = 0;
                    $message = '给已经邀请过的老用户发放抽奖记录';
                } else {
                    $code = 4;
                    $message = '老用户未邀请';
                }

                $back = [
                    'code' => $code,
                    'message' => $message,
                ];

                return $back;
            }
        } else {
            if (Yii::$app->request->isAjax) {
                $mobile = Yii::$app->session->get('duobao_mobile');

                if (Yii::$app->session->hasFlash('duobao_new')) {
                    Yii::$app->session->setFlash('duobao_mobile_signup', $mobile);

                    $back = [
                        'code' => 0,
                        'toUrl' => '/promotion/p1705/signup',
                    ];
                } else {
                    $back = [
                        'code' => 5,
                        'message' => '未登录',
                    ];
                }

                return $back;
            }
        }

        return $this->render('duobao', [
            'share' => $share,
            'jindu' => $jindu,
            'promo' => $promo,
            'promoTime' => $promoTime,
            'isJoinWith' => $isJoinWith,
            'source' => $source,
            'isBind' => $isBind,
            'isZJ' => $isZJ,
            'promoLotteryQuery' => $promoLotteryQuery,
            'totalTicketCount' => $num,
            'joinTicket' => $joinTicket,
        ]);
    }


    /**
     * 注册落地页.
     */
    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark='.time());
        }
        $promo = RankingPromo::findOne(['key' => 'duobao0504']);

        if (null === $promo) {
            throw $this->ex404('活动不存在');
        }
        $captcha = new CaptchaForm();
        $mobile = Yii::$app->session->get('duobao_mobile');
        return $this->render('signup', [
            'captcha' => $captcha,
            'mobile' => $mobile,
            'promo' => $promo,
        ]);
    }

    /**
     * 校验手机号.
     */
    public function actionValidateMobile($key, $mobile)
    {
        if (!Yii::$app->user->isGuest) {
            return [
                'code' => 1,
                'message' => '',
            ];
        }

        $promo = $this->findOr404(RankingPromo::class, ['key' => $key]);
        $promoAtfr = new DuoBao($promo);

        if (empty($mobile)) {
            return [
                'code' => 1,
                'message' => '手机号不能为空',
            ];
        }

        //判断是否为浙江手机号
        $isZJ = $promoAtfr->isZhejiangMobile($mobile);
        if (!$isZJ) {
            $back = [
                'code' => 1,
                'message' => '本次活动只限浙江用户参加',
            ];

            return $back;
        }

        $user = User::findOne(['safeMobile' => SecurityUtils::encrypt($mobile)]);
        Yii::$app->session->set('duobao_mobile', $mobile);

        if (null === $user) {
            $promoMobile = PromoMobile::findOne(['promo_id' => $promo->id, 'mobile' => $mobile]);

            if (null === $promoMobile) {
                PromoMobile::initNew($promo->id, $mobile)->save();     //跳转落地注册页之前,记录用户手机号
            }

            Yii::$app->session->setFlash('duobao_new', $mobile);
        }

        return [
            'code' => 0,
            'message' => '校验手机号成功',
        ];
    }
}
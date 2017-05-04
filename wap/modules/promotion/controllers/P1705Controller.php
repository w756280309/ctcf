<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\Promo201705;
use common\models\promo\PromoLotteryTicket;
use common\models\user\CaptchaForm;
use common\models\user\User;
use common\models\promo\DuoBao;
use common\service\BankService;
use common\utils\SecurityUtils;
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
     * 5.15-5.19周年庆
     */
    public function actionYearDay($wx_share_key = null)
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
     * 5.20周年庆活动
     */
    public function action520Day($wx_share_key = null)
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
        //活动总人数
        $numall = 2000;
        $isEnd = false;
        $user = null;
        $isJoinWith = null;
        $source = null;
        $top10 = null; //最新参加活动的10个用户
        $isBind = null;//新用户帮卡为1；否则为null 用于提示弹窗
        $duobaoCode = null;//夺宝码
        $isZJ = null; //已登陆用户浙江手机号 已登陆用户且手机号为非浙江，直接触发弹窗信息

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }
        $promo = RankingPromo::findOne(['key' => 'promo_201705']);
        $promoAtfr = new DuoBao($promo);
        //获取参与人数
        $num =$promoAtfr->totalTicketCount();
        //计算参与进度  如果总人数不为0 岁计算参与进度 例如 97 页面显示结果97%
        $jindu = ceil($num/($numall/100));

        //获取最近参加的10个用户记录 如果记录<10 则null
        if ($num >= 10) {
            $PromoLotteryQuery = PromoLotteryTicket::find()->where(['promo_id' =>$promo->id])->limit(10)->orderBy('id desc')->all();
        }


        //活动结束
        $isEnd = $promoAtfr->promoTime();

        if (!Yii::$app->user->isGuest){
            $back = [];
            $user = $this->getAuthedUser();
            //获取用户类别
            $source = $promoAtfr->source($user);
            //用户是否参与活动
            $isJoinWith = $promoAtfr->isJoinWith($user);

            //夺宝码展示
            $duobaoCode = PromoLotteryTicket::find()->where(['promo_id' =>$promo->id , 'user_id' =>$user->id])->one();

            //判断手机号是否为浙江号码
            $isZJ = $promoAtfr->isZhejiangMobile(SecurityUtils::decrypt($user->safeMobile));//调用左队长的方法
            //点击参与活动按钮 ajax
            if (Yii::$app->request->isAjax) {

                //判断是否为老客
                if ($source == 'inviter') {
                    //是否已经参与活动
                    $code = $isJoinWith ? 1 : 2 ; //参与 1 未参与 2
                    $message = $isJoinWith ? "已参与" : "未参与";
                    $toUrl = $isJoinWith ? '/' : '/user/invite';
                    $yaoqingBind = true;
                    if (!$isJoinWith) {
                        //没有参与活动的如果邀请列表已经开户
                        $promoAtfr->addTicketForUser($user);
                    }
                }
                $back = [
                    'code' => $code,
                    'message' => $message,
                    'toUrl' => $toUrl,
                ];

               return $back;
            }

            if ($source != 'inviter') {
               //新用户是否帮卡
                $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
                $isBind = BankService::check($user, $cond);

            }

        } else {
            if (Yii::$app->request->isAjax) {
                $mobile = Yii::$app->session->get('duobao_mobile');
                if (Yii::$app->session->hasFlash('duobao_inviter')) {
                    Yii::$app->session->setfash('duobao_mobile_signup', $mobile);
                }


            }
        }
        return $this->render('duobao', [
            'share' => $share,
            'jindu' => $jindu,
            'promo' => $promo,
            'isEnd' => $isEnd,
            'user' => $user,
            'isJoinWith' => $isJoinWith,
            'source' => $source,
            'top10' => $top10,
            'isBind' => $isBind,
            'duobaoCode' => $duobaoCode,//对象
            'isZJ' => $isZJ,
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
        $promo = RankingPromo::findOne(['key' => 'promo_201705']);

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
        $message = '';
        $toUrl = '';
        $code = '';
        $back = [];
        $promo = $this->findOr404(RankingPromo::class, ['key' => $key]);

        $promoAtfr = new DuoBao($promo);

        //已经登录用户不显示弹框
        if (Yii::$app->user->isGuest) {
            if (empty($mobile)) {
                $back = [
                    'code' => 0,
                    'message' => '手机号不能为空',
                    'toUrl' => '/',
                ];
                return $back;
            }

            //判断是否为浙江手机号
            $isZJ = $promoAtfr->isZhejiangMobile($mobile);
            if (!$isZJ) {
                $back = [
                    'code' => 0,
                    'message' => '本次活动只限浙江用户参加',
                    'toUrl' => '/',
                ];
                return $back;
            }

            $user = User::findOne(['safeMobile' => SecurityUtils::encrypt($mobile)]);
            Yii::$app->session->set('duobao_mobile', $mobile);


            if (null !== $user) {
                /*//用户是否帮卡
                $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
                $isBind = BankService::check($user, $cond);*/
                if ($promoAtfr->source($user) == "inviter") {
                    $code = 1;
                    $message = '未登录老用户';
                    $toUrl = '/site/login?next="/promotion/p1705/duobao"';
                } else {
                    $code = 1;
                    $message = '未登录新用户';
                    $toUrl = '/site/login?next="/promotion/p1705/duobao"';
                }

            } else {

                $promoMobile = PromoMobile::findOne(['promo_id' => $promo->id, 'mobile' => $mobile]);

                if (null === $promoMobile) {
                    PromoMobile::initNew($promo->id, $mobile)->save();     //跳转落地注册页之前,记录用户手机号
                }
                $code = 2;
                $message = '未注册用户';
                $toUrl = '/promotion/p1705/signup';
                Yii::$app->session->setFlash('duobao_inviter', $mobile);
            }


        }

        $back = [
            'code' => $code,
            'message' => $message,
            'toUrl' => $toUrl,
        ];
        return $back;
    }


}
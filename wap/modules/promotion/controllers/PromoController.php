<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\order\OnlineOrder;
use common\models\user\CaptchaForm;
use common\models\user\User;
use wap\modules\promotion\models\PromoMobile;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;
use common\utils\SecurityUtils;

/**
 * 首次投资送积分系列活动.
 */
class PromoController extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';
    private $promo = null;

    /**
     * 活动落地页.
     */
    public function actionIndex($key, $wx_share_key = null)
    {
        $promo = $this->promo($key);
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('wrm170210' === $promo->key && !$this->fromWx() ? 'index_wrm' : 'index', [
            'promo' => $promo,
            'share' => $share,
        ]);
    }

    /**
     * 校验手机号.
     */
    public function actionValidateMobile($key, $mobile)
    {
        $promo = $this->promo($key);
        $back = [];

        try {
            if ($promo->isActive()) {
                $back = $this->validate($mobile, $key);
            }
        } catch (\Exception $e) {
            $back = [
                'code' => 1,
                'message' => $e->getMessage(),
                'toUrl' => '',
            ];
        }

        if (empty($back)) {
            Yii::$app->session->set('promo_mobile', $mobile);

            $promoMobile = PromoMobile::findOne(['promo_id' => $promo->id, 'safeMobile' => SecurityUtils::decrypt($mobile)]);

            if (null === $promoMobile) {
                PromoMobile::initNew($promo->id, $mobile)->save();     //跳转落地注册页之前,记录用户手机号
            }

            $back = [
                'code' => 0,
                'message' => '成功',
                'toUrl' => '/promotion/'.$key.'/luodiye',
            ];
        }

        return $back;
    }

    /**
     * 落地注册页.
     */
    public function actionLuodiye($key)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark=' . time());
        }

        $captcha = new CaptchaForm();
        $mobile = Yii::$app->session->get('promo_mobile');
        $promo = $this->promo($key);

        return $this->render('luodiye', [
            'captcha' => $captcha,
            'mobile' => $mobile,
            'promo' => $promo,
            'key' => $key,
        ]);
    }

    /**
     * 落地页成功结果页.
     */
    public function actionBack()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }
        $key = Yii::$app->request->get('key');
        /**
         * @var User $user
         */
        $user = $this->getAuthedUser();
        $isFromWrm = $key === 'wrm170210';
        $isIdVerified = $user->isIdVerified();

        return $this->render('back', [
            'user' => $user,
            'isFromWrm' => $isFromWrm,
            'isIdVerified' => $isIdVerified,
        ]);
    }

    /**
     * 获取首次投资送积分活动信息.
     */
    private function promo($key)
    {
        return $this->promo ?: $this->findOr404(RankingPromo::class, ['key' => $key]);
    }

    /**
     * 判断用户是否是首次投资用户.
     */
    private function isTraded(User $user)
    {
        $count = OnlineOrder::find()
            ->where(['uid' => $user->id])
            ->count();

        return boolval($count);
    }

    /**
     * 校验手机号码.
     */
    private function validate($mobile, $key)
    {
        $message = '';
        $toUrl = '';
        $back = [];

        /**
         * @var User $user
         */
        if (empty($mobile)) {
            $message = '手机号不能为空';
        } else {
            $user = $this->getAuthedUser();

            if ($user) {
                $toUrl = '/';

                if ($this->isTraded($user)) {
                    $message = '您已经投资过了，请查看其他活动';
                } else {
                    if ($user->isIdVerified()) {
                        $message = '您已登录,投资即可获得奖励';
                    } elseif ($key === 'wrm170210') {
                        $message = '您已登录，实名认证/投资领取奖励';
                        $toUrl = '/promotion/promo/back?key='.$key;
                    }
                }
            } else {
                $user = User::findOne([
                    'mobile' => $mobile,
                    'type' => User::USER_TYPE_PERSONAL,
                ]);

                if (null !== $user) {
                    $toUrl = '/site/login?next='.urlencode(Yii::$app->request->hostInfo);

                    if ($this->isTraded($user)) {
                        $message = '您已经投资过了，可登录查看其他活动';
                    } else {
                        $message = '您已注册，登录后投资即可获得奖励';
                    }
                }
            }
        }

        if ($message) {
            $back = [
                'code' => 1,
                'message' => $message,
                'toUrl' => $toUrl,
            ];
        }

        return $back;
    }
}

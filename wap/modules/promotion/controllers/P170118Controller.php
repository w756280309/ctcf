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

//温都猫活动
class P170118Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    //温都猫落地页，在电影券活动基础上更改图片和描述
    public function actionWdm($wx_share_key = null)
    {
        $promo = $this->moviePromo();
        $share = null;

        if (null === $promo) {
            throw $this->ex404('活动不存在');
        }

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('wdm', [
            'share' => $share,
            'promo' => $promo,
        ]);
    }

    /**
     * 校验手机号.
     */
    public function actionValidateMobile($mobile)
    {
        $promo = $this->moviePromo();
        $toUrl = '';

        if (null === $promo) {
            throw $this->ex404('活动不存在');
        }

        try {
            if ($promo->isActive()) {
                if (empty($mobile)) {
                    throw new \Exception('手机号不能为空');
                }

                $user = $this->getAuthedUser();

                if ($user) {
                    $toUrl = '/';

                    if ($this->isTraded($user)) {
                        throw new \Exception('您已经投资过了，请查看其他活动');
                    } else {
                        throw new \Exception('您已登录,投资即可获得奖励');
                    }
                } else {
                    $user = User::findOne([
                        'mobile' => $mobile,
                        'type' => User::USER_TYPE_PERSONAL,
                    ]);

                    if (null !== $user) {
                        $toUrl = '/site/login?next='.urlencode(Yii::$app->request->hostInfo);

                        if ($this->isTraded($user)) {
                            throw new \Exception('您已经投资过了，可登录查看其他活动');
                        } else {
                            throw new \Exception('您已注册，登录后投资即可获得奖励');
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => $e->getMessage(), 'toUrl' => $toUrl];
        }

        Yii::$app->session->set('movie_mobile', $mobile);

        $promoMobile = PromoMobile::findOne(['promo_id' => $promo->id, 'mobile' => $mobile]);

        if (null === $promoMobile) {
            PromoMobile::initNew($promo->id, $mobile)->save();     //跳转落地注册页之前,记录用户手机号
        }

        return ['code' => 0, 'message' => '成功', 'toUrl' => '/promotion/p170118/register'];
    }

    /**
     * 观影落地页.
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark=' . time());
        }

        $promo = $this->moviePromo();

        if (null === $promo) {
            throw $this->ex404('活动不存在');
        }

        $captcha = new CaptchaForm();
        $mobile = Yii::$app->session->get('movie_mobile');

        return $this->render('register', [
            'captcha' => $captcha,
            'mobile' => $mobile,
            'promo' => $promo,
        ]);
    }

    /**
     * 落地页成功结果页.
     */
    public function actionRes()
    {
        $user = $this->getAuthedUser();

        return $this->render('res', [
            'user' => $user,
        ]);
    }

    /**
     * 获取首次投资送观影券活动信息.
     */
    private function moviePromo()
    {
        return RankingPromo::findOne(['key' => 'wendumao']);
    }

    /**
     * 判断用户是否是首次投资用户.
     */
    private function isTraded(User $user)
    {
        $order = OnlineOrder::find()
            ->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])
            ->one();

        return !is_null($order);
    }
}

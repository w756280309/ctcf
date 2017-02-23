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

class P170126Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 首次投资送太空展套票.
     */
    public function actionSpace($wx_share_key = null)
    {
        $promo = $this->spacePromo();
        $share = null;

        if (null === $promo) {
            throw $this->ex404('活动不存在');
        }

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('space', [
            'share' => $share,
            'promo' => $promo,
        ]);
    }

    /**
     * 校验手机号.
     */
    public function actionValidateMobile($mobile)
    {
        $promo = $this->spacePromo();
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

        Yii::$app->session->set('space_mobile', $mobile);

        $promoMobile = PromoMobile::findOne(['promo_id' => $promo->id, 'mobile' => $mobile]);

        if (null === $promoMobile) {
            PromoMobile::initNew($promo->id, $mobile)->save();     //跳转落地注册页之前,记录用户手机号
        }

        return ['code' => 0, 'message' => '成功', 'toUrl' => '/promotion/p170126/luodiye'];
    }

    /**
     * 太空展落地页.
     */
    public function actionLuodiye()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?_mark='.time());
        }

        $promo = $this->spacePromo();

        if (null === $promo) {
            throw $this->ex404('活动不存在');
        }

        $captcha = new CaptchaForm();
        $mobile = Yii::$app->session->get('space_mobile');

        return $this->render('luodiye', [
            'captcha' => $captcha,
            'mobile' => $mobile,
            'promo' => $promo,
        ]);
    }

    /**
     * 落地页成功结果页.
     */
    public function actionBack()
    {
        $user = $this->getAuthedUser();

        return $this->render('back', [
            'user' => $user,
        ]);
    }

    /**
     * 获取首次投资送观影券活动信息.
     */
    private function spacePromo()
    {
        return RankingPromo::findOne(['key' => 'promo_space']);
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
}

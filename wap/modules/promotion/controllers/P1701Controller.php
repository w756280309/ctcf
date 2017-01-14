<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\order\OnlineOrder;
use common\models\user\CaptchaForm;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class P1701Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 首次投资送代金券活动.
     */
    public function actionMovie($wx_share_key = null)
    {
        $promo = $this->moviePromo();
        $share = null;

        if (null === $promo) {
            throw $this->ex404('活动不存在');
        }

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('movie', [
            'share' => $share,
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
                        $toUrl = '/site/login';

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

        return ['code' => 0, 'message' => '成功', 'toUrl' => '/promotion/p1701/luodiye'];
    }

    /**
     * 观影落地页.
     */
    public function actionLuodiye()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('/?mark=' . time());
        }

        $captcha = new CaptchaForm();
        $mobile = Yii::$app->session->get('movie_mobile');

        return $this->render('luodiye', [
            'captcha' => $captcha,
            'mobile' => $mobile,
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
    private function moviePromo()
    {
        return RankingPromo::findOne(['key' => 'promo_movie']);
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
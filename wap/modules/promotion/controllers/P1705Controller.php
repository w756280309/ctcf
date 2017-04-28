<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\models\promo\Promo201705;
use common\models\user\User;
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
     * 5月青年节活动
     */
    public function actionYouthDay($wx_share_key = null)
    {
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }
        $xunzhang = 0;//确认
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
}
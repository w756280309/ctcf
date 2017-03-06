<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use wap\modules\promotion\models\RankingPromo;
use yii\web\Controller;

class P170306Controller extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';

    /**
     * 三八女神节.
     */
    public function actionWomen($wx_share_key = null)
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'women_promo']);
        $share = null;

        if (!empty($wx_share_key)) {
            $share = Share::findOne(['shareKey' => $wx_share_key]);
        }

        return $this->render('women', [
            'share' => $share,
        ]);
    }
}
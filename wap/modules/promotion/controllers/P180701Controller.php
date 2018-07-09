<?php
namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;

class P180701Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /*
     * 初始化页面
     * */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180701']);
        $user = $this->getAuthedUser();
        $data = [
            'isLoggedIn' => null !== $user,
            'promoStatus' => $this->getPromoStatus($promo),
        ];
        $this->renderJsInView($data);

        return $this->render('index');
    }

    /*
     * 抽奖接口
     * */
    public function actionGetReward()
    {
        $key = \Yii::$app->request->get('key');
        $promo = $this->findOr404(RankingPromo::className(), ['key' => $key]);
        $user = $this->getAuthedUser();
        try {
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $ticket = $promoClass->pointDraw($user);

            return [
                'code' => 0,
                'message' => '成功',
                'result' => [
                    'sn' => $ticket->reward->sn,
                ],
            ];
        } catch (\Exception $e) {
            return $this->getErrorByCode($e->getCode());
        }
    }
}

<?php
namespace wap\modules\ctcf\controllers;

use common\models\promo\PromoService;
use wap\modules\promotion\models\RankingPromo;

class P180701Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /*
     * 初始化页面接口
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
            $ticket = PromoService::draw($promo, $user);

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

    /*
     * 获取抽奖状态
     * 查询今日用户已抽奖记录，判断当前状态，然后确定是否可以添加抽奖次数
     * */
    public function actionDrawState()
    {
        $key = \Yii::$app->request->get('key');
        $promo = $this->findOr404(RankingPromo::className(), ['key' => $key]);
        $user = $this->getAuthedUser();
        try {
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $state = $promoClass->getDrawState($user);
            $promoClass->enableAddTicket($state, $user);

            return [
                'code' => 0,
                'message' => '成功',
                'state' => $state,
            ];
        } catch (\Exception $e) {
            return $this->getErrorByCode($e->getCode());
        }
    }
}

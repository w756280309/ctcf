<?php
namespace wap\modules\ctcf\controllers;

use common\models\promo\PromoService;
use common\models\promo\TicketToken;
use wap\modules\promotion\models\RankingPromo;
use Exception;
use Yii;

class P180601Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /*
     * 初始化页面
     * 特殊返回字段说明
     * isHaveDraw 预约抽奖 是否有抽奖机会，true 有  false 无
     * bonus,预约抽奖获得的抽奖金额
     * bonusType,奖金类型 目前积分、现金
     * boxCount 用户活动期间获取的宝箱个数，默认为0;
     * */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180601']);
        $user = $this->getAuthedUser();
        $isLoggedIn = false;
        $isHaveDraw = false;
        $bonus = 0;
        $bonusType = '';
        $annualInvest = 0;
        $boxCount = 0;
        if (null !== $user) {
            $isLoggedIn = true;
            $promoClass = new $promo->promoClass($promo);
            $drawData = $promoClass->getDrawData($user);
            if (!empty($drawData)) {
                $isHaveDraw = $drawData['isDrawn'] ? false : true;
                $bonus = $drawData['isDrawn'] ? $drawData['ref_amount'] : 0;
                $bonusType = $drawData['isDrawn'] ? $drawData['ref_type'] : '';
            }
            $annualInvest = $this->getPromoAnnualInvest($promo, $user);
            $haveReward = $promoClass->getRewardPool($annualInvest/10000);
            $boxResult = array_count_values($haveReward);
            $boxCount = isset($boxResult[1]) ? $boxResult[1]: 0;
        }
        $data = [
            'promoStatus' => $this->getPromoStatus($promo),
            'isLoggedIn' => $isLoggedIn,
		    'isHaveDraw' => $isHaveDraw,
            'bonus' => $bonus,
            'bonusType' => $bonusType,
            'annualInvest' => $annualInvest,
            'boxCount' => $boxCount,
        ];
        $this->renderJsInView($data);

        return $this->render('index');
    }

    /*
     * 预约抽奖接口
     * return json
     * */
    public function actionGetReward()
    {
        $user = $this->getAuthedUser();
        $promo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180601']);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->checkStatus($promo, $user);
            $key = $promo->id . '-' . $user->id . '-' . 'appointment to draw';
            TicketToken::initNew($key)->save(false);
            $ticket = PromoService::draw($promo, $user);
            $transaction->commit();

            return [
                'code' => '0',
                'message' => '成功',
                'sn' => $ticket->reward->sn,
                'refAmount' => $ticket->reward->ref_amount,
                'refType' => $ticket->reward->ref_type,
            ];
        } catch (Exception $e) {
            $transaction->rollBack();
            $code = $e->getCode();

            return $this->getErrorByCode($code);
        }
    }
}

<?php

namespace wap\modules\promotion\controllers;

use common\models\promo\Fest77;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use wap\modules\promotion\models\RankingPromo;

class Fest77InController extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionThird()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
        $user = $this->getAuthedUser();
        try {
            $promo->isActive($user);
        } catch (\Exception $ex) {
            return $this->redirect('index');
        }
        $awardList = [];
        $waitAwarded = false;
        $promoClass = new Fest77($promo);
        if (null !== $user) {
            $awardList = $promoClass->getAwardList($user);
            $waitAwarded = null !== PromoLotteryTicket::fetchOneActiveTicket($promo, $user);
            $awardKeys = $this->getThreeRewardKey();
            foreach ($awardList as $k => $award) {
                $awardList[$k]['note'] = in_array($award['sn'], $awardKeys) ? '第三关' : '第一关';
            }
        }

        //获取当前是否已经领取
        return $this->render('third', [
            'user' => $user,
            'awardList' => $awardList,
            'waitAwarded' => $waitAwarded,
            'isFinishedThree' => $this->getFinishedThree($awardList),
        ]);
    }

    private function getFinishedThree($awardList)
    {
        $rewardKeys = $this->getThreeRewardKey();

        foreach ($awardList as $award) {
            if (in_array($award['sn'], $rewardKeys)) {
                return true;
            }
        }

        return false;
    }

    private function getThreeRewardKey()
    {
        return [
            '170828_coupon_20',
            '170828_coupon_50',
            '170828_card_50',
            '170828_fare_50',
            '170828_scales',
            '170828_p_520',
            '170828_p_77',
            '170828_cash_77',
        ];
    }

    public function actionAwardThree()
    {
        $user = $this->getAuthedUser();
        //活动状态
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
        try {
            $promo->isActive($user);
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            if (in_array($code, [1, 2])) {
                return [
                    'code' => $code,
                    'message' => $ex->getMessage(),
                ];
            }
        }
        if (null === $user) {
            return [
                'code' => 3,
                'message' => '您还未登录！',
            ];
        }

        $promoClass = new Fest77($promo);
        $list = $promoClass->getAwardList($user);
        if (!$this->getFinishedThree($list)) {
            return [
                'code' => 4,
                'message' => '您还没有完成第三关！',
            ];
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $key = $promo->id . '-' . $user->id . '-cash-7.7';
            TicketToken::initNew($key)->save(false);
            $reward = Reward::fetchOneBySn('170828_cash_77');
            PromoService::award($user, $reward, $promo);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            return [
                'code' => 5,
                'message' => $ex->getMessage(),
            ];
        }

        return [
            'code' => 0,
            'message' => '成功',
        ];
    }
}

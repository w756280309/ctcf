<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;

class P171212Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 双12储值送加息券 - 活动落地页
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171212']);
        $user = $this->getAuthedUser();
        $availableBalance = 0;
        if (null !== $user) {
            $account = $user->lendAccount;
            if (null !== $account) {
                $availableBalance = $account->available_balance;
            }
        }
        $this->registerPromoStatusInView($promo);

        return $this->render('index', [
            'availableBalance' => rtrim(rtrim(bcdiv($availableBalance, 10000, 2), '0'), '.'),
        ]);
    }

    /**
     * 双12储值送加息券 - 领走加息券
     * 分为两步逻辑
     * 1）送加息券
     * 2）奖品列表
     */
    public function actionPull()
    {
        $data = [];
        $promo = RankingPromo::findOne(['key' => 'promo_171212']);
        $user = $this->getAuthedUser();

        //判断活动状态及登录状态
        $promoStatus = $this->getPromoStatus($promo);
        if ($promoStatus > 0) {
            return 1 === $promoStatus
                ? $this->getErrorByCode(self::ERROR_CODE_NOT_BEGIN)
                : $this->getErrorByCode(self::ERROR_CODE_ALREADY_END);
        }
        if (null === $user) {
            return $this->getErrorByCode(self::ERROR_CODE_NOT_LOGIN);
        }

        //初始化活动处理类
        $promoClass = new $promo->promoClass($promo);
        try {
            $data = $promoClass->pull($user);
        } catch (\Exception $ex) {
            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }

        return $data;
    }
}

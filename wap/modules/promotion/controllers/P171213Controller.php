<?php

namespace wap\modules\promotion\controllers;

use common\models\promo\PromoService;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use common\models\user\RechargeRecord;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class P171213Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 初始化页面
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171213']);
        $user = $this->getAuthedUser();
        $netRechargeMoney = 0;
        if (null !== $user) {
            $netRechargeMoney = $this->getNetRechargeMoney($user, $promo->startTime, $promo->endTime);
        }
        $this->registerPromoStatusInView($promo);

        return $this->render('index', [
            'netRechargeMoney' => rtrim(rtrim(bcdiv($netRechargeMoney, 10000, 2), '0'), '.'),
        ]);
    }

    private function getNetRechargeMoney($user, $startTime, $endTime)
    {
        $netRechargeMoneyQuery = RechargeRecord::find()
            ->where(['uid' => $user->id])
            ->andWhere(['pay_type' => RechargeRecord::PAY_TYPE_NET])
            ->andWhere(['status' => RechargeRecord::STATUS_YES])
            ->andFilterWhere(['>=', 'created_at', strtotime($startTime)]);
        if (null !== $endTime) {
            $netRechargeMoneyQuery->andFilterWhere(['<=', 'created_at', strtotime($endTime)]);
        }

        return (float) $netRechargeMoneyQuery->sum('fund');
    }

    /**
     * 点击领取页面
     */
    public function actionPull()
    {
        //判断参数
        $type = (int) Yii::$app->request->get('type');
        if (!in_array($type, [1, 2])) {
            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }

        //判断活动是否存在
        $promo = RankingPromo::findOne(['key' => 'promo_171213']);
        if (null === $promo) {
            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }

        //判断活动状态
        $promoStatus = null;
        $user = $this->getAuthedUser();
        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }
        if (null !== $promoStatus) {
            return 1 === $promoStatus
                ? $this->getErrorByCode(self::ERROR_CODE_NOT_BEGIN)
                : $this->getErrorByCode(self::ERROR_CODE_ALREADY_END);
        }

        //判断登录状态
        if (null === $user) {
            return $this->getErrorByCode(self::ERROR_CODE_NOT_LOGIN);
        }

        //判断网银充值金额
        $flag = false;
        $netRechargeMoney = $this->getNetRechargeMoney($user, $promo->startTime, $promo->endTime);
        if (1 === $type) {
            if (bccomp($netRechargeMoney, 0, 2) <= 0) {
                $flag = true;
            }
        } else {
            if (bccomp($netRechargeMoney, 100000, 2) < 0) {
                $flag = true;
            }
        }
        if ($flag) {
            return $this->getErrorByCode(self::ERROR_CODE_NEVER_GOT_TICKET);
        }

        $rewardSns = $this->getRewardSns($type);
        $promoClass = new $promo->promoClass($promo);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $key = $promo->id . '-' . $user->id . '-' . $type;
            TicketToken::initNew($key)->save(false);
            foreach ($rewardSns as $rewardSn) {
                $reward = Reward::fetchOneBySn($rewardSn);
                PromoService::award($user, $reward, $promo);
            }
            $transaction->commit();
            return [
                'code' => self::STATUS_SUCCESS,
                'message' => '成功',
                'ticket' => $promoClass->getRewardList($user, $rewardSns),
            ];
        } catch (\yii\db\IntegrityException $ex) {
            $transaction->rollBack();
            if (23000 === $ex->getCode()) {
                return [
                    'code' => self::ERROR_CODE_NO_TICKET,
                    'message' => '您已经领取过了',
                    'ticket' => $promoClass->getRewardList($user, $rewardSns),
                ];
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }

        return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
    }

    private function getRewardSns($type)
    {
        $sns = [];
        if (1 === $type) {
            return [
                'wy_c_10',
                'wy_bc_05',
                'wy_p_16',
            ];
        } elseif (2 === $type) {
            return [
                'wy_c_30',
                'wy_bc_10',
                'wy_p_66',
            ];
        }

        return $sns;
    }
}

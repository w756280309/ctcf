<?php

namespace common\models\promo;

use common\models\user\User;
use yii\helpers\ArrayHelper;

class Promo171212 extends BasePromo
{
    public function pull(User $user)
    {
        $avaliableBalance = $user->lendAccount->available_balance;
        $sns = $this->getSnsByAmount($avaliableBalance);
        $currentAwardList = $this->getAwardList($user);
        $gotSns = ArrayHelper::getColumn($currentAwardList, 'sn');
        $waitSns = array_diff($sns, $gotSns);
        $flag = false;
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($waitSns as $sn) {
                $reward = Reward::fetchOneBySn($sn);
                $key = $this->promo->id . '-' . $user->id . '-' . $sn;
                TicketToken::initNew($key)->save(false);
                PromoService::award($user, $reward, $this->promo);
                $flag = true;
            }
            $transaction->commit();
        } catch (\yii\db\IntegrityException $ex) {
            if ('23000' !== $ex->getCode()) {
                throw $ex;
            }
            $transaction->rollBack();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }

        if (true === $flag) {
            $currentAwardList = $this->getAwardList($user);
        }
        $awardCount = count($currentAwardList);

        return [
            'num' => $awardCount,
            'award' => $currentAwardList,
        ];
    }

    private function getSnsByAmount($amount)
    {
        $sns = [];
        $levelInfo = [
            '50000' => 'bonus_05_7',
            '100000' => 'bonus_10_7',
            '200000' => 'bonus_15_7',
            '500000' => 'bonus_25_7',
            '1000000' => 'bonus_30_7',
        ];

        foreach ($levelInfo as $levelAmount => $sn) {
            if ($amount >= $levelAmount) {
                array_push($sns, $sn);
            }
        }

        return $sns;
    }
}
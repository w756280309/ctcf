<?php

namespace common\ctcf\promo;

use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder;
use common\models\promo\BasePromo;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use common\models\user\User;
use yii\db\Query;

class Promo180814 extends BasePromo
{
    //获取步数对应的奖品
    private function getRewardPool($base)
    {
        return [
            '180814_C10' => intval($base >= 2),
            '180814_C50' => intval($base >= 6),
            '180814_P77' => intval($base >= 15),
            '180814_RP7.7' => intval($base >= 15),
        ];
    }

    //更加用户步数进行发奖
    public function awardUserBySteps($user)
    {
        $awards = [];
        $addResult = $this->enableAddSteps($user);
        $steps = $this->getRecordQuery($user)->sum('quantity');
        $rewards = $this->getRewardPool($steps);
        foreach ($rewards as $sn => $hasReward) {
            if ($hasReward) {
                $ticket = $this->fetchOneActiveTicket($user, $sn);
                if (!$ticket) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        $key = $this->promo->id . '-' . $user->id . '-' . $sn;
                        TicketToken::initNew($key)->save(false);
                        PromoLotteryTicket::initNew($user, $this->promo, $sn)->save(false);
                        $reward = Reward::fetchOneBySn($sn);
                        PromoService::award($user, $reward, $this->promo, $ticket);
                        $awards[] = $sn;
                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        \Yii::info($e, 'promo_log');
                    }
                }
            }
        }

        return [
            'awards' => $awards,
            'addResult' => $addResult,
        ];
    }

    //能否增添步数 能则添加
    private function enableAddSteps(User $user)
    {
        $addFree = false;
        $addShare = false;
        $today = date('Y-m-d');
        //能否添加每日助力
        $freeCount = $this->getRecordQuery($user, 'free', $today)->count();
        if ($freeCount < 1) {
            //免费添加记录
            $key = $this->promo->id . '-free-' . $user->id . '-' .$today;
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                TicketToken::initNew($key)->save(false);
                $this->addRecord($user, '每日助力', 1);
                $transaction->commit();
                $addFree = true;
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
        //能否添加分享分享助力
        $shareCount = $this->getRecordQuery($user, 'share', $today)->count();
        if ($shareCount < 1) {
            $shareRecord = (new Query())
                ->from('share_log')
                ->where(['createdAt' => $today])
                ->andWhere(['userId' => $user->id])
                ->andWhere(['scene' => 'timeline'])
                ->andWhere(['like', 'shareUrl', 'p180814'])
                ->count();
            if ($shareRecord > 0) {
                //添加分享记录
                $key = $this->promo->id . '-share-' . $user->id . '-' .$today;
                $transactionShare = \Yii::$app->db->beginTransaction();
                try {
                    TicketToken::initNew($key)->save(false);
                    $this->addRecord($user, '分享助力', 1, 'share');
                    $transactionShare->commit();
                    $addShare = true;
                } catch (\Exception $ex) {
                    $transactionShare->rollBack();
                    throw $ex;
                }
            }
        }

        return [
            'addFree' => $addFree,
            'addShare' => $addShare,
        ];
    }

    //获取是否存在记录
    public function getRecordQuery($user = null, $source = null, $createTime = null, $isRead = null)
    {
        $query = (new Query())
            ->from('promo_record')
            ->where([
                'promoId' => $this->promo->id,
            ]);
        if (isset($user)) {
            $query->andWhere(['userId' => $user->id]);
        }
        if (isset($source)) {
            $query->andWhere(['source' => $source]);
        }
        if (isset($isRead)) {
            $query->andWhere(['isRead' => $isRead]);
        }
        if (isset($createTime)) {
            $query->andWhere(['date(createTime)' => $createTime]);
        }

        return $query;
    }

    //处理步数数据
    public function dealRecord(User $user)
    {
        $stepRecord = $this->getRecordQuery($user)
            ->select('createTime as date, quantity as step, note')
            ->orderBy(['createTime' => SORT_DESC])
            ->all();
        $stepCount = $this->getRecordQuery($user, null, null, false)->count();
        if ($stepCount > 0) {
            $this->updateRecord($user);
        }

        return $stepRecord;
    }

    private function addRecord(User $user, $note = null, $quantity = null, $source = 'free')
    {
        $sql = 'INSERT INTO 
                    promo_record(`promoId`, `userId`, `note`, `source`, `quantity`, `createTime`) 
                VALUES 
                  (:promoId, :userId, :note, :source, :quantity, :createTime)';
        \Yii::$app->db->createCommand($sql, [
            'promoId' => $this->promo->id,
            'userId' => $user->id,
            'note' => $note,
            'source' => $source,
            'quantity' => $quantity,
            'createTime' => date('Y-m-d H:i:s')
        ])->execute();

        return true;
    }

    private function updateRecord(User $user)
    {
        $sql = 'UPDATE 
                  promo_record
                SET 
                  isRead = 1
                WHERE
                  promoId = :promoId
                AND
                  userId = :userId';
        \Yii::$app->db->createCommand($sql, [
            'promoId' => $this->promo->id,
            'userId' => $user->id,
        ])->execute();

        return true;
    }

    public function doAfterOrderSuccess($order)
    {
        //当购买标的为新手标时，返回
        if ($order->loan->is_xs === 1) {
            return false;
        }
        if ($order instanceof OfflineOrder) {
            return false;
        }
        $user = $order->user;
        if ($order->status !== OnlineOrder::STATUS_SUCCESS
            || !$this->promo->isActiveInEvent($user, $order->order_time)
        ) {
            return false;
        }
        $this->addRecord($user, '投资助力', 5, 'invest');

        return true;
    }
}

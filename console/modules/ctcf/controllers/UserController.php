<?php

namespace console\modules\ctcf\controllers;

use common\models\order\OnlineOrder;
use common\models\tx\UserAsset;
use common\models\user\UserAccount;
use yii\console\Controller;
use Yii;

class UserController extends Controller
{
    /**
     * 更新账户余额、理财金额
     */
    public function actionAccount($update = false)
    {
        $userAssets = UserAsset::find()
            ->select("user_id, sum(amount/100) as investment_balance")
            ->where(["isRepaid" => false])
            ->andWhere(['>', "amount", 0])
            ->groupBy('user_id')
            ->asArray()
            ->all();
        if ($update) {
            foreach ($userAssets as $userAsset) {
                Yii::$app->db->createCommand('update user_account set account_balance = available_balance + :investment_balance , investment_balance = :investment_balance where uid = :uid')
                    ->bindValues([':investment_balance' => $userAsset['investment_balance'], ':uid' => $userAsset['user_id']])
                    ->execute();
            }
        } else {
            $result = 'id,type,uid,account_balance,available_balance,freeze_balance,profit_balance,investment_balance,drawable_balance,in_sum,out_sum,created_at,updated_at' . PHP_EOL;
            foreach ($userAssets as $userAsset) {
                $userAccount = UserAccount::find()->where(['uid' => $userAsset['user_id']])->asArray()->one();
                $userAccount['account_balance'] = $userAccount['available_balance'] + $userAsset['investment_balance'];
                $userAccount['investment_balance'] = $userAsset['investment_balance'];
                $result .= implode(',', $userAccount) . PHP_EOL;
            }
            echo $result;
        }
    }

    /**
     * 更新收益金额
     */
    public function actionProfit($update = false)
    {
        $ua = UserAsset::tableName();
        $oo = 'ctcf_main.' . OnlineOrder::tableName();
        $userAssets = UserAsset::find()
            ->select("$ua.user_id, sum($oo.order_money*$oo.yield_rate*$oo.expires/365) as profit_balance")
            ->leftJoin($oo, "$oo.id = $ua.order_id")
            ->where(["isRepaid" => true])
            ->andWhere(["isInvalid" => false])
            ->groupBy('user_id')
            ->asArray()
            ->all();

        if ($update) {
            foreach ($userAssets as $userAsset) {
                Yii::$app->db->createCommand('update user_account set profit_balance = :profit_balance where uid = :uid')
                    ->bindValues([':profit_balance' => $userAsset['profit_balance'], ':uid' => $userAsset['user_id']])
                    ->execute();
            }
        } else {
            $result = 'id,type,uid,account_balance,available_balance,freeze_balance,profit_balance,investment_balance,drawable_balance,in_sum,out_sum,created_at,updated_at' . PHP_EOL;
            foreach ($userAssets as $userAsset) {
                $userAccount = UserAccount::find()->where(['uid' => $userAsset['user_id']])->asArray()->one();
                $userAccount['profit_balance'] = $userAsset['profit_balance'];
                $result .= implode(',', $userAccount) . PHP_EOL;
            }
            echo $result;
        }
    }
}

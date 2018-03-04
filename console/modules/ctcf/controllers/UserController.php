<?php

namespace console\modules\ctcf\controllers;

use common\models\order\OnlineOrder;
use common\models\tx\UserAsset;
use common\models\user\User;
use common\models\user\UserAccount;
use common\utils\SecurityUtils;
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

        foreach ($userAssets as $k => $v) {
            $user = User::findOne($v['user_id']);
            if (false !== strpos($user->username, 'ctcf')) {
                $userAssets[$k]['profit_balance'] = bcadd($v['profit_balance'], 0, 2);
            } else {
                unset($userAssets[$k]);
            }
        }

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

    /**
     * 所有用户的手机号、姓名、是否在老站投资过
     */
    public function actionInvestlist($update = false)
    {
        $posts = Yii::$app->db->createCommand("
                SELECT user.safeMobile,user.real_name,user.safeIdCard,user.birthdate,user.username,IF(invest.isInvested is null,'否','是') as isInvested
                FROM user
                LEFT JOIN (SELECT distinct online_order.uid,1 as isInvested
                FROM online_order
                INNER JOIN online_product on online_product.id = online_order.online_pid
                WHERE online_product.sn like 'CTCF-LEGACY-%') as invest
                ON invest.uid = user.id
                WHERE user.username like 'ctcf:%'
                ")
            ->queryAll();
        $arr = [];
        foreach ($posts as $k => $v) {
            $old_user_id = str_replace('ctcf:', '', $v['username']);
            $user = Yii::$app->db->createCommand("
                SELECT user_phone,
                       real_name,
                       id_card,
                       user_age,
                       user_sex
                FROM t_safety_certification
                WHERE user_id = :user_id
                ")
                ->bindValue(':user_id', $old_user_id)
                ->queryOne();
            $arr[$k]['mobile'] = empty($v['safeMobile']) ? $user['user_phone'] : SecurityUtils::decrypt($v['safeMobile']);
            $arr[$k]['name'] = empty($v['real_name']) ? $user['real_name'] : $v['real_name'];
            $id_card = empty($v['safeIdCard']) ? $user['id_card'] : SecurityUtils::decrypt($v['safeIdCard']);
            $arr[$k]['sex'] = empty($id_card) ? '' : (substr($id_card, 16,1) % 2 == 0 ? '女' : '男');
            $arr[$k]['birthday'] = empty($id_card) ? '' : date('Y-m-d', strtotime(substr($id_card, 6,8)));
            $arr[$k]['isInvested'] = $v['isInvested'];
        }
        $result = '手机号,姓名,性别,生日,是否在老站投资过' . PHP_EOL;
        foreach ($arr as $val) {
            $result .= implode(',', $val) . PHP_EOL;
        }
        echo $result;
    }
}

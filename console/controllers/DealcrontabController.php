<?php
/**
 * 定时任务文件.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51.
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\models\user\UserAccount;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use common\service\LoanService;
use common\models\order\OrderManager;
use common\models\order\CancelOrder;
use common\utils\TxUtils;

class DealcrontabController extends Controller
{
    /**
     * 定时满标
     * 1）存在其他推荐标的时，将该标的推荐时间置为0（即取消推荐状态）//2018-03-26   只要满标就取消推荐（不考虑有无其他推荐标）
     * 2）募集中状态 2 修改为 3 满标（已售罄）状态
     * 3）循环更新每个投资者账户的理财资产（+）及冻结金额（-）
     * 4）创建满标资金流水
     * 5）满标超投部分撤标
     */
    public function actionFull()
    {
        //查询出3条待满标的募集中标的记录
        $loans = OnlineProduct::find()
            ->where([
                'finish_rate' => 1,
                'status' => 2,
            ])->orderBy(['recommendTime' => SORT_ASC])
            ->limit(3)
            ->all();

        $db = Yii::$app->db;
        foreach ($loans as $loan) {
            //1）判断是否有其他推荐标，若存在，则将该标推荐时间置为0
            //2018-03-26,直接将满标的标的取消推荐
            $recommendTime = $loan->recommendTime;
            if ($recommendTime > 0) {
                    $recommendTime = 0;
            }

            $transaction = $db->beginTransaction();
            try {
                //2）更新标的状态 募集中状态 "2" 到 满标 "3"
                $updateLoanStatusSql = "update online_product set status = :status,recommendTime = :recommendTime,sort=:fullSort where status = 2 and id = :id";
                $affectRows = $db->createCommand($updateLoanStatusSql, [
                    'status' => OnlineProduct::STATUS_FULL,
                    'recommendTime' => $recommendTime,
                    'id' => $loan->id,
                    'fullSort' => OnlineProduct::SORT_FULL,
                ])->execute();
                if (0 === $affectRows) {
                    $transaction->rollBack();
                    throw new \Exception('更新标的状态失败：募集中 到 满标');
                }

                //查询标的成功的投资记录
                $orders = $loan->successOrders;
                foreach ($orders as $order) {
                    //3）更新每个投资者账户的理财资产（+）及冻结金额（-）
                    $updateAccountSql = "update user_account set investment_balance = investment_balance + :investmentBalance, freeze_balance = freeze_balance - :freezeBalance where type = 1 and uid = :uid";
                    $affectAccountRows = $db->createCommand($updateAccountSql, [
                        'investmentBalance' => $order->order_money,
                        'freezeBalance' => $order->paymentAmount,
                        'uid' => $order->uid,
                    ])->execute();
                    if (0 === $affectAccountRows) {
                        throw new \Exception('更新账户理财资产及冻结金额失败：募集中 到 满标');
                    }

                    //4）创建满标资金流水 type = 7
                    $ua = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $order->uid]);
                    $moneyRecord = new MoneyRecord();
                    $moneyRecord->account_id = $ua->id;
                    $moneyRecord->sn = TxUtils::generateSn('MR');
                    $moneyRecord->type = MoneyRecord::TYPE_FULL_TX;
                    $moneyRecord->osn = $order->sn;
                    $moneyRecord->uid = $order->uid;
                    $moneyRecord->balance = $ua->available_balance;
                    $moneyRecord->in_money = $order->order_money;
                    $moneyRecord->remark = '项目满标,冻结金额转入理财金额账户。交易金额' . $order->order_money;
                    $moneyRecord->save(false);
                }
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                throw $ex;
            }

            //5）超投部分-撤标逻辑（暂时不动）
            OrderManager::findInvalidOrders($loan);
        }
    }

    private function existOtherRecommendLoan($loanId)
    {
        return null !== OnlineProduct::find()
            ->where(['isPrivate' => 0, 'del_status' => 0])
            ->andFilterWhere(['>', 'recommendTime', 0])
            ->andWhere(['<>', 'id', $loanId])
            ->one();
    }

    /**
     * 定时 修改预告期为募集期
     */
    public function actionNow()
    {
        $loans = OnlineProduct::find()->where(' online_status=1 and status=1 and start_date<='.time())->all();
        foreach ($loans as $loan) {
            $resp = Yii::$container->get('ump')->getLoanInfo($loan->id);
            if ($resp->isSuccessful() && '0' === $resp->get('project_state')) {
                $upres = LoanService::updateLoanState($loan, 2);
            }
        }
    }

    /**
     * 定时 修改募集期状态为流标状态【取消】.
     */
    public function actionLiu()
    {
        exit;
        $product = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE, 'status' => OnlineProduct::STATUS_NOW])->andFilterWhere(['<', 'end_date', time()])->all();
        $bc = new BcRound();
        bcscale(14);
        foreach ($product as $val) {
            $order = OnlineOrder::find()->where(['online_pid' => $val['id'], 'status' => OnlineOrder::STATUS_SUCCESS])->all();
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($order as $v) {
                $ua = UserAccount::findOne(['uid' => $v['uid']]);
                $ua->freeze_balance = $bc->bcround(bcsub($ua->freeze_balance, $v['order_money']), 2);
                $ua->available_balance = $bc->bcround(bcadd($ua->available_balance, $v['order_money']), 2);
                $ua->drawable_balance = $bc->bcround(bcadd($ua->drawable_balance, $v['order_money']), 2);
                $ua->in_sum = $bc->bcround(bcadd($ua->in_sum, $v['order_money']), 2);
                if (!$ua->save()) {
                    $transaction->rollBack();

                    return false;
                }

                $v->status = OnlineOrder::STATUS_CANCEL;
                if (!$v->save()) {
                    $transaction->rollBack();

                    return false;
                }

                $money_record = new MoneyRecord();
                $money_record->sn = MoneyRecord::createSN();
                $money_record->type = MoneyRecord::TYPE_ORDER;
                $money_record->osn = $v->sn;
                $money_record->account_id = $ua->id;
                $money_record->uid = $v['uid'];
                $money_record->balance = $ua->available_balance;
                $money_record->in_money = $v['order_money'];

                if (!$money_record->save()) {
                    $transaction->rollBack();

                    return false;
                }
            }

            $val->scenario = 'status';
            $val->status = OnlineProduct::STATUS_LIU;
            $val->sort = OnlineProduct::SORT_LIU;
            if (!$val->save()) {
                $transaction->rollBack();

                return false;
            }
            $transaction->commit();
        }

        return true;
    }

    public function actionAckcancelord()
    {
        $cancelOrd = CancelOrder::find()->where(['txStatus' => 1])->all();
        foreach ($cancelOrd as $ord) {
            try {
                OrderManager::ackCancelOrder($ord);
            } catch (\Exception $ex) {
                //TODO
            }
        }
    }
}

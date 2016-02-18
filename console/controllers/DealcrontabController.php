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

class DealcrontabController extends Controller
{
    /**
     * 定时 刷新满标 满标生成还款计划.
     */
    public function actionFull()
    {
        $data = OnlineProduct::find()->where(['finish_rate' => 1, 'status' => 2])->all();
        $bc = new BcRound();
        foreach ($data as $dat) {
            $pid = $dat['id'];
            OnlineProduct::updateAll(['status' => 3, 'sort' => OnlineProduct::SORT_FULL], ['id' => $pid]);

            $orders = OnlineOrder::getOrderListByCond(['online_pid' => $pid, 'status' => OnlineOrder::STATUS_SUCCESS]);
            foreach ($orders as $ord) {
                $ua = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $ord['uid']]);
                $ua->investment_balance = $bc->bcround(bcadd($ua->investment_balance, $ord['order_money']), 2);
                $ua->freeze_balance = $bc->bcround(bcsub($ua->freeze_balance, $ord['order_money']), 2);
                $ua->save();
            }
        }
    }
    /**
     * 定时 修改预告期为募集期
     */
    public function actionNow()
    {
        $loans = OnlineProduct::find()->where(' online_status=1 and status=1 and start_date<=' . time())->all();
        foreach ($loans as $loan) {
            $resp = Yii::$container->get('ump')->getLoanInfo($loan->id);
            if ($resp->isSuccessful() && '0' === $resp->get('project_state')) {
                $upres = LoanService::updateLoanState($loan, 2);
            }
        }
    }

    /**
     * 定时 修改募集期状态为流标状态
     */
    public function actionLiu()
    {
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
}

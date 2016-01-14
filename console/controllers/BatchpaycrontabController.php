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
use common\models\user\UserAccount;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use PayGate\Cfca\Message\Request1510;
use PayGate\Cfca\Message\Request1520;
use common\lib\cfca\Cfca;
use PayGate\Cfca\Response\Response1520;
use common\models\user\Batchpay;
use common\models\user\DrawRecord;

class BatchpaycrontabController extends Controller
{
    /**
     * 发起批量代付请求
     */
    public function actionLaunch()
    {
        $batchpays = Batchpay::find()->where(['is_launch' => Batchpay::IS_LAUNCH_NO])->all();
        $cfca = new Cfca();
        foreach ($batchpays as $batchpay) {
            $request1510 = new Request1510(Yii::$app->params['cfca']['institutionId'], $batchpay);
            $resp = $cfca->request($request1510);
            if ($resp->isSuccess()) {
                $batchpay->is_launch = Batchpay::IS_LAUNCH_YES;
                $batchpay->save(false);
            }
        }
    }

    /**
     * 次日查询前一日的结果.
     */
    public function actionUpdate()
    {
        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
        $cfca = new Cfca();
        $bc = new BcRound();
        $yesbatchpay = Batchpay::find()->where(['is_launch' => Batchpay::IS_LAUNCH_YES])->andFilterWhere(['between', 'created_at', $beginYesterday, $endYesterday])->all();//
        foreach ($yesbatchpay as $batchpay) {
            $request1520 = new Request1520(Yii::$app->params['cfca']['institutionId'], $batchpay->sn);
            $resp = $cfca->request($request1520);
            $rp1520 = new Response1520($resp->getText());
            $items = $rp1520->getItems();
            foreach ($items as $item) {
                if ($rp1520->isDone($item)) {
                    $batchpayItems = $batchpay->items;
                    $batchpayItem = $batchpayItems[0];
                    $batchpayItem->status = $item['Status'];
                    $batchpayItem->banktxtime = $item['BankTxTime'];
                    $batchpayItem->save(false);
                    $drawRord = DrawRecord::findOne(['sn' => $item['ItemNo']]);
                    $money = bcdiv($item['Amount'], 100, 2);//返回分制转为元制
                    $userAccount = UserAccount::find()->where('uid = '.$drawRord->uid)->one();
                    $userAccount->freeze_balance = $bc->bcround(bcsub($userAccount->freeze_balance, $money), 2);//冻结减少
                    $draw_status = 0;
                    $momeyRecord = new MoneyRecord();
                    //生成一个SN流水号
                    $sn = $momeyRecord::createSN();
                    $momeyRecord->uid = $drawRord->uid;
                    $momeyRecord->sn = $sn;
                    $momeyRecord->account_id = $userAccount->id;
                    if ($rp1520->isSuccess($item)) {
                        //成功的
                        $draw_status = DrawRecord::STATUS_SUCCESS;
                        $YuE = $userAccount->account_balance = $bc->bcround(bcsub($userAccount->account_balance, $money), 2);//账户总额减少
                        $momeyRecord->type = MoneyRecord::TYPE_DRAW;
                        $momeyRecord->balance = $YuE;
                        $momeyRecord->out_money = $money;
                    } else {
                        //失败
                        $draw_status = DrawRecord::STATUS_FAIL;//提现不成功
                        $YuE = $userAccount->account_balance = $bc->bcround(bcadd($userAccount->account_balance, $money), 2);//账户总额增加
                        $userAccount->available_balance = $bc->bcround(bcadd($userAccount->available_balance, $money), 2);//更新可用余额
                        $userAccount->drawable_balance = $bc->bcround(bcadd($userAccount->drawable_balance, $money), 2);//更新提现金额
                        $userAccount->in_sum = $bc->bcround(bcadd($userAccount->available_balance, $money), 2);//更新入账
                        $momeyRecord->type = MoneyRecord::TYPE_DRAW_RETURN;
                        $momeyRecord->balance = $YuE;
                        $momeyRecord->in_money = $money;
                    }
                    $momeyRecord->save();
                    $userAccount->save();
                    $drawRord->status = DrawRecord::STATUS_SUCCESS;
                    $drawRord->save();
                    $batchpay->is_launch = Batchpay::IS_LAUNCH_FINISH;
                }
            }
        }
    }
}

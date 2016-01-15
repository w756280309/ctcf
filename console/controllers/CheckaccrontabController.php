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
use common\lib\bchelp\BcRound;
use common\models\checkaccount\CheckaccountCfca;
use common\models\checkaccount\CheckaccountWdjf;
use common\models\checkaccount\CheckaccountHz;
use common\models\user\RechargeRecord;
use PayGate\Cfca\Message\Request1810;
use common\lib\cfca\Cfca;
use PayGate\Cfca\Response\Response1810;

class CheckaccrontabController extends Controller
{
    /**
     * 获取中金对账单【中金每日凌晨5时生成上一日对账单】
     * 建议在凌晨5时之后执行上一日的对账单.
     */
    public function actionCfca()
    {
        $date = date('Y-m-d', strtotime('-1 day'));//获取前日
        $rq1810 = new Request1810(Yii::$app->params['cfca']['institutionId'], $date);
        $cfca = new Cfca();
        $resp = $cfca->request($rq1810);
        $rp1810 = new Response1810($resp->getText());
        //echo date('Y-m-d H:i:s',strtotime('20150118090808'));exit;
        $connection = \Yii::$app->db;
        $data = array();
        $time = time();
        $notes = $rp1810->getTxs();
        while (list(, $tx) = each($notes)) {
            $banknotificationtime = empty($tx['BankNotificationTime']) ? '0' : date('Y-m-d H:i:s', strtotime($tx['BankNotificationTime']));
            $data[] = [$date, $tx['TxType'], $tx['TxSn'], bcdiv($tx['TxAmount'], 100), $tx['PaymentAmount'], $tx['InstitutionAmount'], $banknotificationtime, $time, $time];
        }
        //var_dump($data);exit;
        if (!empty($data)) {
            $res = $connection->createCommand()->batchInsert(CheckaccountCfca::tableName(), ['tx_date', 'tx_type', 'tx_sn', 'tx_amount', 'payment_amount', 'institution_fee', 'bank_notification_time', 'created_at', 'updated_at'],
                    $data)->execute();
            if ($res) {
                echo 'success';
            } else {
                /////失败代码处理
            }
        }
        exit;
    }

    /**
     * 获取温都金服充值订单前一日【结算在结算定时任务中完成】
     * 建议在凌晨0点至5点之间运行。要保证5点之前执行完毕.
     */
    public function actionWdjf()
    {
        $date = date('Y-m-d', strtotime('-1 day'));//获取前日
        echo $date;
        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
        $is_write = CheckaccountWdjf::find()->where(['tx_date' => $date, 'tx_type' => [1375, 1311]])->count('id');
        if ($is_write) {
            return false;
        }
        
        //筛选快充和pc充值的充值记录
        $dataobj = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_YES, 'pay_type' => [RechargeRecord::PAY_TYPE_QUICK, RechargeRecord::PAY_TYPE_NET]])->andFilterWhere(['between', 'bankNotificationTime', date('Y-m-d H:i:s', $beginYesterday), date('Y-m-d H:i:s', $endYesterday)])->all();
        //var_dump($dataobj);exit;
        $insert_arr = array();
        $time = time();
        foreach ($dataobj as $dat) {
            $tx_type = (RechargeRecord::PAY_TYPE_QUICK === (int) $dat->pay_type) ? 1375 : 1311;
            $insert_arr[] = [$dat->sn, $date, $tx_type, $dat->sn, $dat->fund, 0, 0, $dat->bankNotificationTime,  $time, $time];
        }
        if (!empty($insert_arr)) {
            $connection = \Yii::$app->db;
            $res = $connection->createCommand()->batchInsert(CheckaccountWdjf::tableName(), ['order_no', 'tx_date', 'tx_type', 'tx_sn', 'tx_amount', 'payment_amount', 'institution_fee', 'bank_notification_time', 'created_at', 'updated_at'],
                    $insert_arr)->execute();
            if ($res) {
            } else {
                /////失败代码处理
                echo 'fail';
            }
        }

        return true;
    }

    /**
     * 温度金服的对账单与中金对账单做比对【建议在凌晨5点之后进行】.
     */
    public function actionCompare()
    {
        $date = date('Y-m-d', strtotime('-1 day'));//获取前日
        //echo $date;
        $list = (new \yii\db\Query())
                ->select('w.*,c.tx_amount c_tx_amount')
                ->from([CheckaccountWdjf::tableName().' as w'])
                ->innerJoin(CheckaccountCfca::tableName().' as c', 'c.tx_sn=w.tx_sn')
                ->where(['w.tx_date' => $date, 'c.tx_date' => $date])->all();//只校正交易金额tx_amount
        //var_dump($list);exit;
        $false_ids = array();
        $success_ids = array();
        foreach ($list as $data) {
            if ($data['is_checked'] == 1) {
                //对于已经执行的。不能再执行了
                echo 'has been implemented';
                exit;
            }
            if (bccomp($data['tx_amount'], $data['c_tx_amount']) === 0) {
                $success_ids[] = $data['id'];
            } else {
                $false_ids[] = $data['id'];
            }
        }

        if (!empty($false_ids)) {
            CheckaccountWdjf::updateAll(['is_checked' => 1, 'is_auto_okay' => 2], ['id' => $false_ids]);
        }
        if (!empty($success_ids)) {
            CheckaccountWdjf::updateAll(['is_checked' => 1, 'is_auto_okay' => 1], ['id' => $success_ids]);
        }
        echo 'finished';
    }

    /**
     * 每日汇总对账单【建议在执行完对账之后执行Comparebill】.
     */
    public function actionHz()
    {
        bcscale(14);
        $date = date('Y-m-d', strtotime('-1 day'));//获取前日
        //先判断有没有录入过
        $beginYesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
        $endYesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1);
//        $beginThisMonth=date('Y-m-d', mktime(0,0,0,date('m'),1,date('Y')));//本月的开始日期
//        $endThisMonth=date('Y-m-d', mktime(0,0,0,date('m'),date('t'),date('Y')));//本月的结束日期
        $count = CheckaccountHz::find()->filterWhere(['between', 'tx_date', $beginYesterday, $endYesterday])->count();
        if ($count) {
            echo 'has been implemented';
            exit;
        }

        $wdjfobj = CheckaccountWdjf::find()->where('is_checked=1 and (is_auto_okay=1 or (is_auto_okay=2 and is_okay=1))')->andFilterWhere(['between', 'tx_date', $beginYesterday, $endYesterday])->all();
        //var_dump($wdjfobj);exit;
        $recharge_count = $recharge_sum = $jiesuan_count = $jiesuan_sum = 0;
        foreach ($wdjfobj as $obj) {
            if ($obj->tx_type == 1311) {
                //充值
                ++$recharge_count;
                $recharge_sum = bcadd($recharge_sum, $obj->tx_amount);
            } elseif ($obj->tx_type == 1341) {
                //结算
                ++$jiesuan_count;
                $jiesuan_sum = bcadd($jiesuan_sum, $obj->tx_amount);
            }
        }
        $hzmodel = new CheckaccountHz();
        $bcround = new BcRound();
        $hzmodel->tx_date = $date;
        $hzmodel->recharge_count = $recharge_count;
        $hzmodel->recharge_sum = $bcround->bcround($recharge_sum, 2);
        $hzmodel->jiesuan_count = $jiesuan_count;
        $hzmodel->jiesuan_sum = $bcround->bcround($jiesuan_sum, 2);
        if ($hzmodel->validate()) {
            $hzmodel->save();
            echo 'finish!';
        } else {
            print_r($hzmodel->getErrors());
        }
    }
}

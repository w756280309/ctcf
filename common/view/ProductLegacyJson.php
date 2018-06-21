<?php
namespace common\view;

use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use common\models\order\OnlineOrder;
use yii;

class ProductLegacyJson
{
    /*
     * 标的数据展示 网贷与定期
     * @param $deals array
     * return array
     * */
    public static function showProduct($deals)
    {
        $data = [];
        if (!empty($deals)) {
            foreach ($deals as $key => $deal) {
                $isActive = !in_array($deal->status, [OnlineProduct::STATUS_PRE, OnlineProduct::STATUS_NOW]) || $deal->end_date < time();
                $isShowXin = ($deal->is_xs && in_array($deal->status, [1, 2]) && $deal->end_date >= time()) ? 1 : 0;
                $dealStatusMsg = ($deal->end_date < time() && in_array($deal->status, [1, 2])) ? '募集结束':Yii::$app->params['deal_status'][$deal->status];
                $ex = $deal->getDuration();
                $data[$key]['url'] = '/deal/deal/detail?sn='.$deal->sn;
                $data[$key]['title'] = $deal->title;//标题
                $data[$key]['cid'] = $deal->cid;//标的类型
                $data[$key]['isXin'] = $deal->is_xs;//是否是新手
                $data[$key]['isShowXin'] = $isShowXin;//是否展示新手logo
                $data[$key]['dealStatus'] = $deal->status;//标的状态
                $data[$key]['isActive'] = $isActive;//是否是募集完成后状态
                $data[$key]['dealStatusMsg'] = $dealStatusMsg;//标的状态信息
                $data[$key]['tags'] = $deal->tags;//以中文'，'分割,返回字符串
                $data[$key]['pointsMultiple'] = $deal->pointsMultiple;//积分倍数
                $data[$key]['rate'] = LoanHelper::getDealRate($deal);//标的基本利率
                $data[$key]['rateAdd'] = !empty($deal->jiaxi) ? StringUtils::amountFormat2($deal->jiaxi) : '';//加息利率
                $data[$key]['duration'] = $ex['value'];//期限
                $data[$key]['durationUnit'] = $ex['unit'];//单位
                $data[$key]['startMoney'] = StringUtils::amountFormat2($deal->start_money);//起投金额
                $data[$key]['progress'] = $deal->getProgressForDisplay();//募集进度 单位%
                $data[$key]['refundMethod'] = Yii::$app->params['refund_method'][$deal->refund_method];//还款方式
            }
        }

        return $data;
    }

    /*
     * 转让数据展示
     * @param $deals array
     * return array
     * */
    public static function showTransfer($deals)
    {
        $data = [];
        if (!empty($deals)) {
            foreach ($deals as $key => $note) {
                $loan_id = (int) $note['loan_id'];
                $order_id = (int) $note['order_id'];
                $loan = OnlineProduct::findOne($loan_id);
                $order = OnlineOrder::findOne($order_id);
                $endTime = new \DateTime($note['endTime']);
                $nowTime = new \DateTime();
                $isTransfer = $note['isClosed'] || $nowTime >= $endTime;
                $progress = $isTransfer ? 100 : bcdiv(bcmul($note['tradedAmount'], '100'), $note['amount'], 0);

                $data[$key]['url'] = '/credit/note/detail?id='.$note['id'];
                $data[$key]['title'] = $loan->title;
                $data[$key]['isTransfer'] = $isTransfer;//是已经否转让 true 已转让 false 转让中
                $data[$key]['rate'] = StringUtils::amountFormat2(bcmul($order->yield_rate, 100, 2));//预期年化
                $duration = $loan->getRemainingDuration();
                $month = (isset($duration['months']) && $duration['months'] > 0) ? $duration['months']:'';
                $day = (isset($duration['days']) && (!isset($duration['months']) || $duration['days'] > 0)) ? $duration['days']:'';
                $data[$key]['durationM'] = $month;
                $data[$key]['monthUnit'] = '个月';
                $data[$key]['durationD'] = $day;
                $data[$key]['dayUnit'] = '天';
                $data[$key]['amount'] = $note['amount']/100;//转让金额
                $data[$key]['progress'] = $progress;//进度
                $data[$key]['refundMethod'] = Yii::$app->params['refund_method'][$loan->refund_method];//还款方式
            }
        }

        return $data;
    }
}

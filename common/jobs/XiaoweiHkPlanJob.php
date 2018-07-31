<?php

namespace common\jobs;

use common\models\order\OnlineRepaymentPlan;
use Yii;
use yii\base\Object;
use yii\queue\Job;

class XiaoweiHkPlanJob extends Object implements Job
{
    public $loan; //标的

    /**
     * 还款计划同步
     *
     * 请求相对地址：paycenter/micro-financal-callback/repay-plan
     * 请求方式：POST
     * 请求参数：
     *    loanSn string 标的sn
     *    source string 渠道
     *    repayPlan array 还款计划
     *    [
     *        period integer 期数
     *        planPayDate integer 预计还款时间
     *        principal string 还款本金
     *        interest string 还款利息
     *        fee string 手续费
     *    ]
     */
    public function execute($queue)
    {
        $loan = $this->loan;
        if (!$loan->is_jixi) {
            throw new \Exception('标的未计息');
        }
        $asset = $loan->asset;
        if (null === $asset) {
            throw new \Exception('非小微推送资产');
        }
        $source = $asset->source;
        $itemInfo = json_decode($asset->itemInfo, true);

        $wdjfPlans = OnlineRepaymentPlan::find()
            ->where(['online_pid' => $loan->id])
            ->orderBy(['qishu' => SORT_ASC])
            ->all();
        $plans = [];
        foreach ($wdjfPlans as $k => $wdjfPlan) {
            $plans[$k]['period'] = $wdjfPlan->qishu;
            $plans[$k]['planPayDate'] = $wdjfPlan->refund_time;
            $plans[$k]['principal'] = $wdjfPlan->benjin;
            $plans[$k]['interest'] = $wdjfPlan->lixi;
            $plans[$k]['fee'] = 0;
        }

        $params = [
            'loanSn' => $itemInfo[0]['sn'],
            'source' => $source,
            'repayPlan' => $plans,
        ];
        $microClient = Yii::$container->get('micro');
        $responseData = $microClient->doRequest('paycenter/micro-financal-callback/repay-plan', $params);
        Yii::info($responseData, 'xiaowei');
    }
}
<?php

namespace common\jobs;

use common\models\payment\Repayment;
use Yii;
use yii\base\Object;
use yii\queue\Job;

/**
 * 小微还款计划回调job
 *
 * Class XiaoweiHkJob
 * @package common\jobs
 */
class XiaoweiHkJob extends Object implements Job
{
    public $loan; //标的
    public $term; //期数

    /**
     * 还款同步
     *
     * 请求相对地址：paycenter/micro-financal-callback/repay
     * 请求方式：POST
     * 请求参数：
     *    loanSn string 标的sn
     *    period string 期数
     *    principal string 本金
     *    afterInterest string 后收利息
     *    source string 放款时间
     *    afterFee string 渠道
     *    overdueFee string 逾期手续费
     *    penaltyFee string 违约金
     *    repayType string 还款类型 2代偿方还款1正常还款
     *    repayTime integer 时间戳
     */
    public function execute($queue)
    {
        $loan = $this->loan;
        $term = $this->term;
        $asset = $loan->asset;
        if (null === $asset) {
            throw new \Exception('非小微推送资产');
        }
        $source = $asset->source;
        $itemInfo = json_decode($asset->itemInfo, true);
        $repayment = Repayment::find()
            ->where(['loan_id' => $loan->id])
            ->andWhere(['term' => $term])
            ->one();
        if (null !== $repayment) {
            $params = [
                'loanSn' => $itemInfo[0]['sn'],
                'period' => $term,
                'principal' => $repayment->principal,
                'afterInterest' => $repayment->interest,
                'source' => $source,
                'afterFee' => '0',
                'overdueFee' => '0',
                'penaltyFee' => '0',
                'repayType' => $loan->fundReceiver ? '2' : '1',
                'repayTime' => strtotime($repayment->refundedAt),
            ];
            $microClient = Yii::$container->get('micro');
            $responseData = $microClient->doRequest('paycenter/micro-financal-callback/repay' ,$params);
            Yii::info($responseData, 'xiaowei');
        }
    }
}
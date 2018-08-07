<?php

namespace common\jobs;

use Yii;
use yii\base\Object;
use yii\queue\Job;

/**
 * 小微放款状态同步回调job
 *
 * Class XiaoweiFkStatusJob
 * @package common\jobs
 */
class XiaoweiFkStatusJob extends Object implements Job
{
    public $loan; //标的

    /**
     * 放款状态同步
     *
     * 请求相对地址：paycenter/micro-financal-callback/disburse
     * 请求方式：POST
     * 请求参数：
     *    loanSn string 标的sn
     *    amount string 放款金额
     *    beforeFee string 前收手续费（当前为0）
     *    beforeInterest string 前收利息
     *    disburseTime string 放款时间
     *    source string 渠道
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

        $params = [
            'loanSn' => $itemInfo[0]['sn'],
            'amount' => $loan->funded_money,
            'beforeFee' => '0', //前收服务费 温都为0
            'beforeInterest' => '0', //前收利息 温都为0
            'disburseTime' => $loan->fk_examin_time,
            'source' => $source,
        ];
        $microClient = Yii::$container->get('micro');
        $responseData = $microClient->doRequest('paycenter/micro-financal-callback/disburse', $params);
        Yii::info($responseData, 'xiaowei');
    }
}
<?php

namespace common\models\message;

use common\models\order\OnlineRepaymentPlan;
use common\utils\StringUtils;
use Yii;

/**
 * Class RepaymentMessage
 * @package common\models\message
 * @testCase \Test\Common\Models\Message\RepaymentMessageTest
 */
class RepaymentMessage extends WechatMessage
{
    public function __construct(OnlineRepaymentPlan $plan)
    {
        $remainingAmount = $plan->remainingRepaymentAmount();

        if ($remainingAmount) {
            $msg = '该项目剩余回款金额'.StringUtils::amountFormat3($remainingAmount).'元，点击查看详情。';
        } else {
            $msg = '该项目已还清，点击查看详情。';
        }

        $this->data = [
            'first' => ['尊敬的客户，您投资的项目('.$plan->loan->title.')已回款到您的账户，祝您理财愉快。', '#000000'],
            'keyword1' => [StringUtils::amountFormat3($plan->benxi).'元', '#000000'],
            'keyword2' => [StringUtils::amountFormat3($plan->benjin).'元', '#000000'],
            'keyword3' => [StringUtils::amountFormat3($plan->lixi).'元', '#000000'],
            'remark' => [$msg, '#000000'],
        ];
        $this->user = $plan->user;
        $this->templateId = Yii::$app->params['wx.msg_tpl.repayment_success'];

        $asset = $plan->getAsset();
        if (!is_null($asset)) {
            $this->linkUrl = Yii::$app->params['clientOption']['host']['wap'].'user/user/orderdetail?asset_id='. $asset->id;
            $this->linkUrl .= '&utm_campaign=wxmp_notify&utm_source=wxmp_gzh&utm_content=repayment_success';
        }
    }
}

<?php

namespace common\models\message;

use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use Yii;

/**
 * Class OrderMessage
 * @package common\models\message
 * @testCase \Test\Common\Models\Message\OrderMessageTest
 */
class OrderMessage extends WechatMessage
{
    public function __construct(OnlineOrder $order)
    {
        $loan = $order->loan;
        $duration = $loan->getDuration();
        $interest = OnlineProduct::calcExpectProfit($order->order_money,
            $loan->refund_method,
            $duration['value'],
            $order->yield_rate
        );
//        $this->data = [
//            'first' => ['尊敬的客户，您于'.date('Y-m-d H:i:s', $order->order_time).'在温都金服成功投资。', 'black'],
//            'loanTitle' => [$loan->title, 'black'],
//            'apr' => StringUtils::amountFormat2(bcmul($order->yield_rate, 100, 2)).'%',
//            'loanDuration' => $duration['value'].$duration['unit'],
//            'repaymentMethod' => \Yii::$app->params['refund_method'][$loan->refund_method],
//            'orderAmount' => StringUtils::amountFormat3($order->order_money).'元',
//            'interest' => StringUtils::amountFormat3($interest).'元',
//            'remark' => ['感谢您的投资，点击查看详情，如有疑问请致电：400-101-5151进行咨询。', 'black'],
//        ];
        $this->data = [
            'first' => ['尊敬的客户，您于'.date('Y-m-d H:i:s', $order->order_time).'在温都金服成功投资。', '#000000'],
            'keyword1' => [$loan->title, '#000000'],
            'keyword2' => [StringUtils::amountFormat2(bcmul($order->yield_rate, 100, 2)).'%', '#000000'],
            'keyword3' => [$duration['value'].$duration['unit'], '#000000'],
            'keyword4' => [StringUtils::amountFormat3($order->order_money).'元', '#000000'],
            'keyword5' => [StringUtils::amountFormat3($interest).'元', '#000000'],
            'remark' => ['感谢您的投资，点击查看详情，如有疑问请致电：'.Yii::$app->params['contact_tel'].'进行咨询。', '#000000'],
        ];
        $this->user = $order->user;
        $this->linkUrl = Yii::$app->params['clientOption']['host']['wap'].'deal/deal/detail?sn='.$loan->sn;
        $this->linkUrl .= '&utm_campaign=wxmp_notify&utm_source=wxmp_wdjf&utm_content=order_success';
        $this->templateId = Yii::$app->params['wx.msg_tpl.order_success'];
    }
}

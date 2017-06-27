<?php

namespace Test\Common\Models\Message;

use common\models\message\OrderMessage;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\thirdparty\SocialConnect;
use common\models\user\User;

class OrderMessageTest extends SocialMessage
{
    private function getOrderMock(OnlineProduct $loan, User $user)
    {
        $order = $this->getMockBuilder(OnlineOrder::class)
            ->setMethods(['getUser', 'getLoan'])
            ->getMock();

        $order->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));
        $order->expects($this->any())
            ->method('getLoan')
            ->will($this->returnValue($loan));

        return $order;
    }

    public function testMessage()
    {
        $socialConnect = new SocialConnect([
            'user_id' => 10,
            'resourceOwner_id' => 'onjmAv8LRKCNXPvuCt1_huYfiqTg',
            'provider_type' => 'wechat',
        ]);

        $user = $this->getUserMock($socialConnect);
        $loan = new OnlineProduct([
            'id' => 999999,
            'title' => '超级测试标',
            'refund_method' => 1,
            'finish_rate' => 0,
            'jixi_time' => 0,
            'expires' => 365,
            'sn' => 'DK201700004233',
        ]);
        $onlineOrder = $this->getOrderMock($loan, $user);
        $onlineOrder->setAttributes([
            'id' => 10,
            'order_time' => 1496999483,  //2017-06-09 17:11:23
            'order_money' => '10000.00',
            'yield_rate' => '0.10000',
            'online_pid' => 999999,
        ], false);
        $drawMessage = new OrderMessage($onlineOrder);
        $this->assertEquals([
//            [
//                'first' => ['尊敬的客户，您于'.date('Y-m-d H:i:s', $onlineOrder->order_time).'在温都金服成功投资。', 'black'],
//                'loanTitle' => '超级测试标',
//                'apr' => '10%',
//                'loanDuration' => '365天',
//                'repaymentMethod' => '到期本息',
//                'orderAmount' => '10,000.00元',
//                'interest' => '1,000.00元',
//                'remark' => ['感谢您的投资，点击查看详情，如有疑问请致电：400-101-5151进行咨询。', 'black'],
//            ],
            [
                'first' => ['尊敬的客户，您于'.date('Y-m-d H:i:s', $onlineOrder->order_time).'在温都金服成功投资。', '#000000'],
                'keyword1' => ['超级测试标', '#000000'],
                'keyword2' => ['10%', '#000000'],
                'keyword3' => ['365天', '#000000'],
                'keyword4' => ['10,000.00元', '#000000'],
                'keyword5' => ['1,000.00元', '#000000'],
                'remark' => ['感谢您的投资，点击查看详情，如有疑问请致电：400-101-5151进行咨询。', '#000000'],
            ],
            \Yii::$app->params['clientOption']['host']['wap'].'deal/deal/detail?sn=DK201700004233&utm_campaign=wxmp_notify&utm_source=wxmp_wdjf&utm_content=order_success',
            \Yii::$app->params['wx.msg_tpl.order_success'],
            'onjmAv8LRKCNXPvuCt1_huYfiqTg',
        ], [
            $drawMessage->getData(),
            $drawMessage->getLinkUrl(),
            $drawMessage->getTemplateId(),
            $drawMessage->getOpenIdByUser(),
        ]);
    }
}

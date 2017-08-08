<?php

namespace Test\Common\Models\Message;

use common\models\message\RepaymentMessage;
use common\models\order\OnlineRepaymentPlan;
use common\models\product\OnlineProduct;
use common\models\thirdparty\SocialConnect;
use common\models\tx\UserAsset;
use common\models\user\User;

class RepaymentMessageTest extends SocialMessage
{
    private function getRepaymentMock(User $user, OnlineProduct $loan, UserAsset $asset, $remainingRepaymentAmount)
    {
        $repayment = $this->getMockBuilder(OnlineRepaymentPlan::class)//类名
        ->setMethods(['getUser', 'getLoan', 'remainingRepaymentAmount', 'getAsset'])
            ->getMock(); //创建桩件

        $repayment->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $repayment->expects($this->any())
            ->method('getLoan')
            ->will($this->returnValue($loan));

        $repayment->expects($this->any())
            ->method('getAsset')
            ->will($this->returnValue($asset));

        $repayment->expects($this->any())
            ->method('remainingRepaymentAmount')
            ->will($this->returnValue($remainingRepaymentAmount));

        return $repayment;
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
            'title' => '测试回款标的',
        ]);
        $userAsset = new UserAsset([
            'id' => 101,
        ]);

        $repayment = $this->getRepaymentMock($user, $loan, $userAsset, '1234.00');
        $repayment->benxi = 1100;
        $repayment->lixi = 100;
        $repayment->benjin = 1000;

        $repaymentMessage = new RepaymentMessage($repayment);

        $linkUrl = \Yii::$app->params['clientOption']['host']['wap'] . 'user/user/orderdetail?asset_id=101&utm_campaign=wxmp_notify&utm_source=wxmp_wdjf&utm_content=repayment_success';

        $config = [
            'data' => [
                'first' => ['尊敬的客户，您投资的项目(测试回款标的)已回款到您的账户，祝您理财愉快。', '#000000'],
                'keyword1' => ['1,100.00元', '#000000'],
                'keyword2' => ['1,000.00元', '#000000'],
                'keyword3' => ['100.00元', '#000000'],
                'remark' => ['该项目剩余回款金额1,234.00元，点击查看详情。', '#000000'],
            ],
            'linkUrl' => $linkUrl,
            'openId' => 'onjmAv8LRKCNXPvuCt1_huYfiqTg',
            'templateId' => \Yii::$app->params['wx.msg_tpl.repayment_success'],
        ];

        $this->assertEquals($repaymentMessage->getParams(), $config);

        $repayment = $this->getRepaymentMock($user, $loan, $userAsset, '0');
        $repayment->benxi = 1100;
        $repayment->lixi = 100;
        $repayment->benjin = 1000;

        $repaymentMessage = new RepaymentMessage($repayment);

        $config['data']['remark'] = ['该项目已还清，点击查看详情。', '#000000'];

        $this->assertEquals($repaymentMessage->getParams(), $config);
    }
}

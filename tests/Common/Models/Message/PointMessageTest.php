<?php

namespace Test\Common\Models\Message;

use common\models\mall\PointRecord;
use common\models\message\PointMessage;
use common\models\thirdparty\SocialConnect;
use common\models\user\User;

class PointMessageTest extends SocialMessage
{
    private function getPointRecardMock(User $obj)
    {
        $point = $this->getMockBuilder(PointRecord::class)//类名
            ->setMethods(['getUser'])
            ->getMock(); //创建桩件

        $point->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($obj));

        return $point;
    }

    public function testMessage()
    {
        $socialConnect = new SocialConnect([
            'user_id' => 10,
            'resourceOwner_id' => 'onjmAv8LRKCNXPvuCt1_huYfiqTg',
            'provider_type' => 'wechat',
        ]);

        $user = $this->getUserMock($socialConnect);
        $point = $this->getPointRecardMock($user);

        $point->setAttributes([
            'ref_type' => PointRecord::TYPE_WECHAT_CONNECT,
            'incr_points' => 10,
            'user_id' => 10,
        ], false);

        $pointMessage = new PointMessage($point);
        $this->assertEquals([
            [
                'first' => ['恭喜您成功绑定账户，获得10积分奖励！', '#000000'],
                'keyword1' => [10, '#000000'],
                'keyword2' => ['绑定账户奖励', '#000000'],
            ],
            null,
            \Yii::$app->params['wx.msg_tpl.add_points_for_connect_wx'],
            'onjmAv8LRKCNXPvuCt1_huYfiqTg',
        ], [
            $pointMessage->getData(),
            $pointMessage->getLinkUrl(),
            $pointMessage->getTemplateId(),
            $pointMessage->getOpenIdByUser(),
        ]);
    }
}

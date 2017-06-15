<?php

namespace Test\Common\Models\Message;

use common\models\message\DrawMessage;
use common\models\thirdparty\SocialConnect;
use common\models\user\DrawRecord;
use common\models\user\User;

class DrawMessageTest extends SocialMessage
{
    private function getDrawMock(User $obj)
    {
        $draw = $this->getMockBuilder(DrawRecord::class)//类名
        ->setMethods(['getUser'])
            ->getMock(); //创建桩件

        $draw->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($obj));

        return $draw;
    }

    public function testMessage()
    {
        $socialConnect = new SocialConnect([
            'user_id' => 10,
            'resourceOwner_id' => 'onjmAv8LRKCNXPvuCt1_huYfiqTg',
            'provider_type' => 'wechat',
        ]);

        $user = $this->getUserMock($socialConnect);
        $drawRecord = $this->getDrawMock($user);

        $drawRecord->setAttributes([
            'id' => 10,
            'created_at' => 1496999483,  //2017-06-09 17:11:23
            'money' => '1000',
            'uid' => 10,
        ], false);

        $drawMessage = new DrawMessage($drawRecord);
        $this->assertEquals([
            [
                'first' => ['尊敬的客户，您申请的提现成功，资金已到达银行卡，请注意查看。', 'black'],
                'keyword1' => ['2017-06-09 17:11:23', 'black'],
                'keyword2' => ['1,000.00元', 'black'],
                'remark' => ['如有疑问请致电:400-101-5151进行咨询。', 'black'],
            ],
            null,
            \Yii::$app->params['draw_message_template_id'],
            'onjmAv8LRKCNXPvuCt1_huYfiqTg',
        ], [
            $drawMessage->getData(),
            $drawMessage->getLinkUrl(),
            $drawMessage->getTemplateId(),
            $drawMessage->getOpenIdByUser(),
        ]);
    }
}

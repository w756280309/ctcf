<?php

namespace Test\Common\Models\Message;

use common\models\thirdparty\SocialConnect;
use Test\YiiAppTestCase;
use common\models\user\User;

class SocialMessage extends YiiAppTestCase
{
    protected function getUserMock(SocialConnect $obj)
    {
        $user = $this->getMockBuilder(User::class)
            ->setMethods(['getSocialConnect'])
            ->getMock(); //创建桩件

        $user->expects($this->any())
            ->method('getSocialConnect')
            ->will($this->returnValue($obj));

        return $user;
    }
}

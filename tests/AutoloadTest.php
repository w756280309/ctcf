<?php

use common\models\user\User;

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testUser()
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user);
    }
}

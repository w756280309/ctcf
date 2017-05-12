<?php

namespace Test\Common\Models\User;

use common\models\user\User;
use Test\YiiAppTestCase;

class UserTest extends YiiAppTestCase
{
    /**
     * 验证IP地址对应的位置信息在不同输入的情况下,返回是否正确.
     */
    public function testRegLocation()
    {
        $user = new User();
        $locations = [
            '未知' => [
                'country' => '未分配或者内网IP',
                'country_id' => 'IANA',
                'region' => '',
                'city' => '',
            ],
            '中国/北京市/北京市' => [
                'country' => '中国',
                'country_id' => '86',
                'region' => '北京市',
                'city' => '北京市',
            ],
            '中国' => [
                'country' => '中国',
                'country_id' => '86',
                'region' => '',
                'city' => '',
            ],
            '中国/北京市' => [
                'country' => '中国',
                'country_id' => '86',
                'region' => '北京市',
                'city' => '',
            ],
            '中国/北京' => [
                'country' => '中国',
                'country_id' => '86',
                'region' => '',
                'city' => '北京',
            ],
        ];
        foreach ($locations as $key => $location) {
            $user->setRegLocation($location);
            $this->assertEquals($user->regLocation, $key);
        }
    }
}
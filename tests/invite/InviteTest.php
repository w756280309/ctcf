<?php

use yii\web\Request;
use yii\web\Cookie;
use yii\web\CookieCollection;
use common\models\invite\InviteHelper;
use common\models\invite\Invite;
use common\models\user\User;
use Test\YiiAppTestCase;

/**
 * 邀请好友测试.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class InviteTest extends YiiAppTestCase
{
    /**
     * 测试是否是有效邀请链接
     * 资料参考：https://phpunit.de/manual/current/zh_cn/test-doubles.html.
     */
    public function testIsInvited()
    {
        $stubrequest = $this->getInviterMock();
        $this->assertEquals('yes', InviteHelper::extractToken($stubrequest));//检查token是否有效
    }

    /**
     * 创建一个邀请数据是否成功
     */
    public function testCreateInvite()
    {
        $user = new User(['id' => 345]);
        $this->assertTrue(Invite::initNew($user)->validate());
    }

    private function getInviterMock()
    {
        $stubrequest = $this->getMockBuilder(Request::class)//类名
                         ->setMethods(array('getCookies'))
                         ->getMock(); //创建桩件

        $cookiesCollection = $this->setCookieCollection();

        $stubrequest->expects($this->once())
            ->method('getCookies')
            ->will($this->returnValue($cookiesCollection));

        return $stubrequest;
    }

    /**
     * 设置cookie集合.
     *
     * @return CookieCollection
     */
    private function setCookieCollection()
    {
        $cookies = [];
        $cookiesarr = [
            [
                'name' => 'yqm',
                'value' => 'yes',
            ],
        ];
        foreach ($cookiesarr as $args) {
            $cookies[$args['name']] = new Cookie($args);
        }
        $cookiesCollection = new CookieCollection($cookies);

        return $cookiesCollection;
    }
}

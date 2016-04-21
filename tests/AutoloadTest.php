<?php

use common\models\user\User;

use yii\di\Container;
use Yii;

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Yii::createObject([
            'class' => 'yii\\web\\Application',
            'id' => 'test',
            'basePath' => dirname(__FILE__),
            'components' => [
                'db' => [
                    'class' => 'yii\\db\\Connection',
                    'dsn' => 'mysql:host=localhost;dbname=wdjf',
                    'username' => 'wdjf',
                    'password' => 'wdjf',
                    'charset' => 'utf8',
                ],
            ],
        ]);
    }

    protected function tearDown()
    {
        Yii::$app = null;
        Yii::$container = new Container();
        parent::tearDown();
    }

    public function testUser()
    {
        $user = new User();
        $this->assertEquals('id', User::primaryKey()[0]);

        $this->assertInstanceOf(User::class, $user);
    }
}

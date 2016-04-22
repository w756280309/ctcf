<?php

namespace Test;

use PHPUnit_Framework_TestCase;
use WdjfTests;
use yii\di\Container;
use Yii;

class YiiAppTestCase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Yii::createObject(WdjfTests::$config);
    }

    protected function tearDown()
    {
        Yii::$app = null;
        Yii::$container = new Container();

        parent::tearDown();
    }
}

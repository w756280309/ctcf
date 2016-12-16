<?php
namespace Test\Lib\StringUtilsTest;

use Test\YiiAppTestCase;
use common\utils\StringUtils;

class StringUtilsTest extends YiiAppTestCase
{
    /**
     * 验证15位身份证号一切正常的情况下,隐藏功能函数是否功能正常
     */
    public function testIdcardOf15()
    {
        $idCardNo = '130503670401001';

        $res = StringUtils::obfsIdCardNo($idCardNo);

        $this->assertEquals('******670401***', $res);
    }

    /**
     * 验证18位身份证号一切正常的情况下,隐藏功能函数是否功能正常
     */
    public function testIdcardOf18()
    {
        $idCardNo = '130721199908087890';

        $res = StringUtils::obfsIdCardNo($idCardNo);

        $this->assertEquals('******19990808****', $res);
    }

    /**
     * 验证身份证号无效的情况下,隐藏功能函数是否功能正常
     */
    public function testInvalidIdcard()
    {
        $idCardNo = [
            '',
            130721199907083456,
        ];

        foreach ($idCardNo as $val) {
            $this->assertEquals('', StringUtils::obfsIdCardNo($val));
        }
    }

    /**
     * 验证身份证号长度不为15或18时,隐藏功能函数是否功能正常
     */
    public function testShortIdcard()
    {
        $idCardNo = '13072119990';

        $res = StringUtils::obfsIdCardNo($idCardNo);

        $this->assertEquals($idCardNo, $res);
    }

    /**
     * 验证11位手机号一切正常的情况下,隐藏功能函数是否功能正常
     */
    public function testMobile()
    {
        $mobile = '13056786789';

        $res = StringUtils::obfsMobileNumber($mobile);

        $this->assertEquals('130******89', $res);
    }

    /**
     * 验证当手机号为空或不是字符串格式时,隐藏功能函数是否功能正常
     */
    public function testInvalidMobile()
    {
        $mobile = [
            '',
            13056786789
        ];

        foreach ($mobile as $val) {
            $this->assertEquals('', StringUtils::obfsMobileNumber($mobile));
        }
    }

    /**
     * 验证当手机号长度不对时,隐藏功能函数是否功能正常
     */
    public function testShortMobile()
    {
        $mobile = '13078934';

        $res = StringUtils::obfsMobileNumber($mobile);

        $this->assertEquals($mobile, $res);
    }

    /**
     * 验证中英文混合的字符串
     */
    public function testMixUtf8Strlen()
    {
        $string = 'sssss莫荻史阳刘凤君左玉玮肖阿勇ss';

        $len = StringUtils::utf8Strlen($string);

        $this->assertEquals($len, 16.5);
    }

    /**
     * 验证纯中文的字符串
     */
    public function testCHSUtf8Strlen()
    {
        $string = '比昂比昂点点滴滴';

        $len = StringUtils::utf8Strlen($string);

        $this->assertEquals($len, 8);
    }
}
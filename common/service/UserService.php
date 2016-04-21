<?php

namespace common\service;

use common\models\user\UserAccount;

/**
 * Desc 主要用于用户模块的服务
 * Created by Pingter.
 * User: Pingter
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class UserService    //暂时没有地方在用该类
{
    const TYPE_TOUZI = 1;//投资用户
    const TYPE_RONGZI = 2;//融资用户

    /**
     * 用户提现验证
     *
     * @param $type 用户类型,$uid 用户uid,$money 提现金额
     *
     * @example $us = new \common\service\UserService();
     $re = $us->checkDraw($this->uid, 99970222.23);
     * @return bool
     */
    public function checkDraw($uid = 0, $money = 0, $type = 1)
    {
        if (empty($uid) || empty($money) ||  !in_array($type, [self::TYPE_TOUZI,  self::TYPE_RONGZI])) {
            return ['code' => 1, 'message' => '数据传输错误'];
        }

        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $money)) {
            return ['code' => 1, 'message' => '金额格式错误'];
        }

        $ua = UserAccount::findOne(['uid' => $uid, 'type' => $type]);
        $diff = bccomp($ua->available_balance, $money, 2);//此函数比较二个高精确度的数字。输入二个字符串，若二个字符串一样大则返回 0；
                                                    //若左边的数字字符串 (left operand) 比右边 (right operand) 的大则返回 +1；
                                                    //若左边的数字字符串比右边的小则返回 -1。scale 是一个可有可无的选项，表示返回值的
                                                    //小数点后所需的位数。
        if ($diff < 0) {
            return ['code' => 1, 'message' => '超出可提现金额'];
        }

        return ['code' => 0, 'message' => '成功'];
    }
}

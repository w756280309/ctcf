<?php

namespace common\service;

use Yii;
use yii\web\Response;
use common\models\user\User;
use common\models\user\QpayBinding;
use common\models\user\UserBanks;
use yii\helpers\ArrayHelper;

/**
 * Desc 主要用于充值提现流程规则的校验
 * Created by Pingter.
 * User: Pingter
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class BankService
{
    const IDCARDRZ_VALIDATE_Y = 1;  //pow(2,0)   验证实名认证已经完成的情况
    const IDCARDRZ_VALIDATE_N = 2;  //pow(2,1)   验证实名认证未完成的情况
    const BINDBANK_VALIDATE_Y = 4;  //pow(2,2)   验证绑定银行卡成功的情况
    const BINDBANK_VALIDATE_N = 8;  //pow(2,3)   验证未绑定银行卡的情况
    const CHARGEPWD_VALIDATE_N = 16;  //pow(2,4)   验证交易密码未设定的情况
    const CHARGEPWD_VALIDATE_Y = 32;  //pow(2,5)   验证交易密码已设定的情况
    const EDITBANK_VALIDATE = 64;     //pow(2,6)   验证是否需要完善银行信息

    /*调用实例
     *     $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE;
            $data = BankService::check($cond);
            if($data[code] == 1) {
                return $data;
            }
     */

    public function __construct()
    {
    }

    /**
     * 验证快捷支付流程.
     * @param object $user 用户对象
     * @param $cond 查询条件
     */
    public static function check($user, $cond)
    {
        if (($cond & self::IDCARDRZ_VALIDATE_Y) && $user->idcard_status == User::IDCARD_STATUS_PASS) {
            return ['tourl' => '/user/user', 'code' => 1, 'message' => '您已经开通第三方资金托管账户'];
        }

        if (($cond & self::IDCARDRZ_VALIDATE_N) && $user->idcard_status == User::IDCARD_STATUS_WAIT) {
            return ['tourl' => '/user/userbank/kuaijie', 'code' => 1, 'message' => '您还没有开通第三方资金托管账户，请前往开通'];
        }

        $user_bank = $user->qpay;
        if (($cond & self::BINDBANK_VALIDATE_Y) && !empty($user_bank)) {
            return ['tourl' => '/user/user', 'code' => 1, 'message' => '您已经成功绑定过银行卡'];
        }

        //此段路放置于最后
        if (($cond & self::BINDBANK_VALIDATE_N) && empty($user_bank)) {
            $bind = QpayBinding::find()->where(['uid' => $user->id])->orderBy('id desc')->one();
            if (null === $bind) {
                return ['tourl' => '/user/userbank/bindbank', 'code' => 1, 'message' => '您还未绑定银行卡，请先去绑定'];
            }
            //如果没有显示card_id 并且绑卡申请是处理中的
            if (QpayBinding::STATUS_ACK === (int)$bind->status) {
                return ['code' => 1, 'message' => '您的绑卡请求正在处理中,请先去转转吧'];
            }
        }

//        if (($cond & self::CHARGEPWD_VALIDATE_N) && empty($user->trade_pwd)) {
//            return ['tourl' => '/user/userbank/addbuspass', 'code' => 1, 'message' => '您未设定交易密码'];
//        }
//
//        if (($cond & self::CHARGEPWD_VALIDATE_Y) && !empty($user->trade_pwd)) {
//            return ['tourl' => '/user/user', 'code' => 1, 'message' => '您已设定交易密码'];
//        }
//
//        if (($cond & self::EDITBANK_VALIDATE) && (empty($user_bank->sub_bank_name) || empty($user_bank->province) || empty($user_bank->city))) {
//            return ['tourl' => '/user/userbank/editbank', 'code' => 1, 'message' => '您需要先完善银行卡信息'];
//        }

        return ['code' => 0];
    }

    /**
     * 根据卡号自动匹配开户行.
     *
     * @param $card_no
     *
     * @return bool
     */
    public static function checkBankcard($card = null)
    {
        if (empty($card)) {
            return ['code' => 1, 'message' => 'card参数错误'];
        }

        $bankList = Yii::$app->params['bank'];
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach ($bankList as $key => $val) {
            $card_8 = substr($card, 0, 8);
            if (isset($val['bin'][$card_8])) {
                $data = explode('-', $val['bin'][$card_8]);
                if ($data[1] == '借记卡') {
                    return ['code' => 0, 'bank_id' => $key, 'bank_name' => $val['bankname']];
                } else {
                    return ['code' => 1, 'message' => '该操作只支持借记卡'];
                }
            }

            $card_6 = substr($card, 0, 6);
            if (isset($val['bin'][$card_6])) {
                $data = explode('-', $val['bin'][$card_6]);
                if ($data[1] == '借记卡') {
                    return ['code' => 0, 'bank_id' => $key, 'bank_name' => $val['bankname']];
                } else {
                    return ['code' => 1, 'message' => '该操作只支持借记卡'];
                }
            }
            $card_5 = substr($card, 0, 5);
            if (isset($val['bin'][$card_5])) {
                $data = explode('-', $val['bin'][$card_5]);
                if ($data[1] == '借记卡') {
                    return ['code' => 0, 'bank_id' => $key, 'bank_name' => $val['bankname']];
                } else {
                    return ['code' => 1, 'message' => '该操作只支持借记卡'];
                }
            }
            $card_4 = substr($card, 0, 4);
            if (isset($val['bin'][$card_4])) {
                $data = explode('-', $val['bin'][$card_4]);
                if ($data[1] == '借记卡') {
                    return ['code' => 0, 'bank_id' => $key, 'bank_name' => $val['bankname']];
                } else {
                    return ['code' => 1, 'message' => '该操作只支持借记卡'];
                }
            }
        }

        return ['code' => 0, 'bank_id' => '', 'bank_name' => ''];
    }

    /**
     * 验证快捷支付全流程是否完成
     * @param object $user 用户对象
     * @return array
     */
    public static function checkKuaijie($user)
    {
        $cond = 0 | self::IDCARDRZ_VALIDATE_N | self::BINDBANK_VALIDATE_N;//删除| self::CHARGEPWD_VALIDATE_N验证交易密码判断
        return self::check($user, $cond);
    }
}

<?php
namespace common\service;

use Yii;
use yii\web\Response;
use common\models\user\User;
use common\models\user\UserBanks;

/**
 * Desc 主要用于充值提现流程规则的校验
 * Created by Pingter.
 * User: Pingter
 * Date: 15-11-19
 * Time: 下午4:02
 */
class BankService {
    const IDCARDRZ_VALIDATE_Y = 1;  //pow(2,0)   验证实名认证已经完成的情况
    const IDCARDRZ_VALIDATE_N = 2;  //pow(2,1)   验证实名认证未完成的情况
    const BINDBANK_VALIDATE_Y = 4;  //pow(2,2)   验证绑定银行卡成功的情况
    const BINDBANK_VALIDATE_N = 8;  //pow(2,3)   验证未绑定银行卡的情况
    const CHARGEPWD_VALIDATE_N = 16;  //pow(2,4)   验证交易密码未设定的情况
    const CHARGEPWD_VALIDATE_Y = 32;  //pow(2,5)   验证交易密码已设定的情况
    const EDITBANK_VALIDATE = 64;     //pow(2,6)   验证是否需要完善银行信息
   // const KUAIJIE_VALIDATE_N = 128;   //pow(2,7)   验证没有开通快捷支付功能的情况

    /*调用实例
     *     $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE;
            $data = BankService::check($cond);
            if($data[code] == 1) {
                return $data;
            }
     */

    public function __construct() {
    }

    /**
     * 验证流程
     * @param $cond
     * @return boolean
     */
    public static function check($uid=null,$cond=null) {
        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE])->one();
//        if(($cond & BankService::KUAIJIE_VALIDATE_N) && $user->kuaijie_status == User::KUAIJIE_STATUS_N) {
//            return ['tourl' => '/user/userbank/kuaijie','code' => 1, 'message' => '您尚未开通快捷支付功能'];
//        }

        if(($cond & BankService::IDCARDRZ_VALIDATE_Y) && $user->idcard_status == User::IDCARD_STATUS_PASS) {
            return ['tourl' => '/user/user','code' => 1, 'message' => '您已经实名认证'];
        }

        if(($cond & BankService::IDCARDRZ_VALIDATE_N) && $user->idcard_status == User::IDCARD_STATUS_WAIT) {
            return ['tourl' => "/user/userbank/kuaijie", 'code' => 1, 'message' => '您未进行实名认证'];
        }

        $user_bank = UserBanks::find()->where(['uid' => $uid])->one();
        if(($cond & BankService::BINDBANK_VALIDATE_Y) && $user_bank && $user_bank->status == UserBanks::STATUS_YES) {
            return ['tourl' => '/user/user','code' => 1, 'message' => '您已经成功绑定过银行卡'];
        }

        if(($cond & BankService::BINDBANK_VALIDATE_N) && (!$user_bank || $user_bank->status != UserBanks::STATUS_YES)) {
            return ['tourl' => "/user/userbank/bindbank", 'code' => 1, 'message' => '您未绑定银行卡'];
        }

        if(($cond & BankService::CHARGEPWD_VALIDATE_N) && !$user->trade_pwd) {
            return ['tourl' => "/user/userbank/addbuspass", 'code' => 1, 'message' => '您未设定交易密码'];
        }

        if(($cond & BankService::CHARGEPWD_VALIDATE_Y) && $user->trade_pwd) {
            return ['tourl' => "/user/user", 'code' => 1, 'message' => '您已设定交易密码'];
        }

        if(($cond & BankService::EDITBANK_VALIDATE) && (!$user_bank->sub_bank_name || !$user_bank->province || !$user_bank->city)) {
            return ['tourl' => "/user/userbank/editbank", 'code' => 1, 'message' => '您需要先完善银行卡信息'];
        }

        return ['code'=>0, 'user' => $user, 'user_bank' => $user_bank];
    }

    /**
     * 根据卡号自动匹配开户行
     * @param $card_no
     * @return boolean
     */
    public static function checkBankcard($card=null) {
        if(!$card) {
            return ['code' => 1, 'message' => 'card参数错误'];
        }

        $bankList = Yii::$app->params['bank'];
        Yii::$app->response->format = Response::FORMAT_JSON;
        foreach($bankList as $key => $val) {
            $card_8 = substr($card, 0, 8);
            if (isset($val['bin'][$card_8])) {
                $data = explode('-', $val['bin'][$card_8]);
                if($data[1] == '借记卡') {
                    return ['code' => 0, 'bank_id' => $key, 'bank_name' => $val['bankname']];
                } else {
                    return ['code' => 1, 'message' => '该操作目前只支持借记卡'];
                }
            }

            $card_6 = substr($card, 0, 6);
            if (isset($val['bin'][$card_6])) {
                $data = explode('-', $val['bin'][$card_6]);
                if($data[1] == '借记卡') {
                    return ['code' => 0, 'bank_id' => $key, 'bank_name' => $val['bankname']];
                } else {
                    return ['code' => 1, 'message' => '该操作目前只支持借记卡'];
                }
            }
            $card_5 = substr($card, 0, 5);
            if (isset($val['bin'][$card_5])) {
                $data = explode('-', $val['bin'][$card_5]);
                if($data[1] == '借记卡') {
                    return ['code' => 0, 'bank_id' => $key, 'bank_name' => $val['bankname']];
                } else {
                    return ['code' => 1, 'message' => '该操作目前只支持借记卡'];
                }
            }
            $card_4 = substr($card, 0, 4);
            if (isset($val['bin'][$card_4])) {
                $data = explode('-', $val['bin'][$card_4]);
                if($data[1] == '借记卡') {
                    return ['code' => 0, 'bank_id' => $key, 'bank_name' => $val['bankname']];
                } else {
                    return ['code' => 1, 'message' => '该操作目前只支持借记卡'];
                }
            }
        }

        return ['code' => 0, 'bank_id' => '', 'bank_name' => ''];
    }

    public static function checkKuaijie($uid=null) {
        if(empty($uid)) {
            return ['code' => 1, 'message' => 'uid参数错误'];
        }

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N;

        $data = self::check($uid,$cond);
        if($data[code] == 1) {
            return $data;
        }
//        else {
//            $user = $data['user'];
//            $user->scenario = 'kuaijie';
//            $user->kuaijie_status = User::KUAIJIE_STATUS_Y;
//            $user->save();
            return ['code' => 0, 'message' => '您已经成功开通快捷支付功能'];
//        }
    }

}

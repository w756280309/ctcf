<?php

namespace common\service;

use common\models\coupon\UserCoupon;
use common\models\product\OnlineProduct;
use Exception;

/**
 * Desc 主要用于购买标的环节的验证
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class PayService
{
    private $postmethod = null;//允许提交方式1ajax 2post
    private $cdeal = null; //当前标的
    public function __construct($method = null)
    {
        $this->postmethod = $method;
    }

    const REQUEST_AJAX = 1;//ajax提交
    const REQUEST_POST = 2;//post提交

    /*定义错误级别*/
    const ERROR_SUCCESS = 0;
    const ERROR_NO_EXIST = 101;//不存在标的
    const ERROR_STATUS_DENY = 102;//状态不可投
    const ERROR_NO_BEGIN = 103;//没有开始 根据时间
    const ERROR_OVER = 104;//已经结束 根据时间
    const ERROR_MONEY_FORMAT = 105;//金额格式错误 根据时间
    const ERROR_LESS_START_MONEY = 106;//投资金额小于起投金额
    const ERROR_MONEY_LESS = 107;//余额不足
    const ERROR_MONEY_MUCH = 108;//投资金额大于可投余额
    const ERROR_DIZENG = 109;//不满足递增要求
    const ERROR_MONEY_BALANCE = 110;//最后一笔需要投满标的
    const ERROR_PRO_STATUS = 111;//更新满标状态错误
    const ERROR_CONTRACT = 112;//合同错误
    const ERROR_TARGET = 113;//定向标只针对目标用户投标
    const ERROR_LOGIN = 114;//需要登录
    const ERROR_TRADE_PWD_EMPTY = 115;//交易密码不能为空
    const ERROR_TRADE_PWD_FALSE = 116;//交易密码错误
    const ERROR_TRADE_PWD_SET = 117;//没有设置交易密码
    const ERROR_ID_SET = 118;//需要实名认证
    const ERROR_BANK_BIND = 119;//需要绑定银行卡

    const ERROR_ORDER_CREATE = 190;//订单生成失败
    const ERROR_UA = 191;//用户账户异常
    const ERROR_UA_CAL = 191;//用户账户扣除异常
    const ERROR_MR = 192;//资金记录异常
    const ERROR_LAW = 198;//非法请求
    const ERROR_SYSTEM = 199;//系统错误

    /**
     * 获取错误内容.
     *
     * @param type $code 错误代码
     *
     * @return string
     */
    public static function getErrorByCode($code = 100)
    {
        $data = [
            self::ERROR_SUCCESS => '',
            self::ERROR_NO_EXIST => '无法找到此标的',
            self::ERROR_STATUS_DENY => '此标的现在不可投',
            self::ERROR_NO_BEGIN => '项目尚未开始或者已经结束',
            self::ERROR_OVER => '项目已经结束',
            self::ERROR_MONEY_FORMAT => '金额格式错误',
            self::ERROR_LESS_START_MONEY => '投资金额小于起投金额',
            self::ERROR_MONEY_LESS => '余额不足',
            self::ERROR_MONEY_MUCH => '投资金额大于可投余额',
            self::ERROR_DIZENG => '不满足递增要求',
            self::ERROR_MONEY_BALANCE => '最后一笔需要投满标的',
            self::ERROR_PRO_STATUS => '更新满标状态错误',
            self::ERROR_CONTRACT => '合同错误',
            self::ERROR_TARGET => '定向标只针对目标用户投标',
            self::ERROR_LOGIN => '请登录',
            self::ERROR_TRADE_PWD_EMPTY => '交易密码不能为空',
            self::ERROR_TRADE_PWD_FALSE => '交易密码错误',
            self::ERROR_TRADE_PWD_SET => '没有设置交易密码',
            self::ERROR_ID_SET => '您还没有进行实名认证',
            self::ERROR_ORDER_CREATE => '创建订单失败',
            self::ERROR_UA => '用户账户异常',
            self::ERROR_UA_CAL => '用户账户扣除异常',
            self::ERROR_MR => '用户资金记录生成异常',
            self::ERROR_LAW => '非法请求',
            self::ERROR_SYSTEM => '系统异常，请稍后重试',
            self::ERROR_BANK_BIND => '您未绑定银行卡',
        ];

        return $data[$code];
    }

    public function checkCommonCond($user = null, $sn = null)
    {
        if (null === $user) {
            return ['code' => self::ERROR_LOGIN,  'message' => self::getErrorByCode(self::ERROR_LOGIN), 'tourl' => '/site/login'];
        }
        if (!in_array($this->postmethod, [1, 2])) {
            return ['code' => self::ERROR_LAW,  'message' => self::getErrorByCode(self::ERROR_LAW)];
        }
        if ($this->postmethod == 1 && !\Yii::$app->request->isAjax) {
            return ['code' => self::ERROR_LAW,  'message' => self::getErrorByCode(self::ERROR_LAW)];
        }
        if ($this->postmethod == 2 && !\Yii::$app->request->isPost) {
            return ['code' => self::ERROR_LAW,  'message' => self::getErrorByCode(self::ERROR_LAW)];
        }

        if (empty($user->idcard_status)) {
            return ['code' => self::ERROR_ID_SET,  'message' => self::getErrorByCode(self::ERROR_ID_SET), 'tourl' => '/user/userbank/idcardrz'];
        }
        if ($user->status == 0) {
            return ['code' => self::ERROR_ID_SET,  'message' => '账户已被冻结', 'tourl' => '/site/usererror'];
        }

        $bankret = BankService::checkKuaijie($user);
        if (0 !== $bankret['code']) {
            return $bankret;
        }

        $deal = OnlineProduct::findOne(['sn' => $sn]);
        $time = time();
        if (!$deal) {
            return ['code' => self::ERROR_NO_EXIST,  'message' => self::getErrorByCode(self::ERROR_NO_EXIST)];
        } elseif ($deal->status != OnlineProduct::STATUS_NOW) {
            return ['code' => self::ERROR_STATUS_DENY,  'message' => self::getErrorByCode(self::ERROR_STATUS_DENY)];
        } elseif ($deal->start_date > $time) {
            return ['code' => self::ERROR_NO_BEGIN,  'message' => self::getErrorByCode(self::ERROR_NO_BEGIN)];
        } elseif ($deal->end_date < $time) {
            return ['code' => self::ERROR_OVER,  'message' => self::getErrorByCode(self::ERROR_OVER)];
        }

        $this->cdeal = $deal;

        //是否定向标
        if (false === LoanService::isUserAllowed($deal, $user)) {
            return ['code' => self::ERROR_SYSTEM,  'message' => '该项目为定向标投资项目，您未获得投资资格'];
        }

        $resp = \Yii::$container->get('ump')->getLoanInfo($deal->id);
        if (!$resp->isSuccessful() && '1' !== $resp->get('project_state')) {
            //查询失败，或者标的状态不为投资中
            return ['code' => self::ERROR_SYSTEM,  'message' => '联动一侧标的状态异常'];
        }

        return true;
    }

    /**
     * 验证是否允许支付.
     *
     * @param type $sn
     * @param type $money
     * @param type $tradepwd
     *
     * @return type
     */
    public function checkAllowPay($user, $sn = null, $money = null, $coupon = null)
    {
        $commonret = $this->checkCommonCond($user, $sn);
        if ($commonret !== true) {
            return $commonret;
        }

        if (empty($money)) {
            return ['code' => self::ERROR_MONEY_FORMAT,  'message' => self::getErrorByCode(self::ERROR_MONEY_FORMAT)];
        }
        if (!preg_match('/^[0-9]+(\.[0-9]+)?$/', $money)) {
            return ['code' => self::ERROR_MONEY_FORMAT,  'message' => self::getErrorByCode(self::ERROR_MONEY_FORMAT)];
        }

        //代金券检验
        $couponMoney = 0;
        if ($coupon) {
            $couponMoney = $coupon->couponType->amount;
            try {
                UserCoupon::checkAllowUse($coupon, $money, $user);
            } catch (Exception $ex) {
                return ['code' => 1,  'message' => $ex->getMessage()];
            }
        }

        if (bccomp(bcadd($user->lendAccount->available_balance, $couponMoney, 2), $money, 2) < 0) {
            return ['code' => 1,  'message' => '金额不足'];
        }
        $orderbalance = $this->cdeal->getLoanBalance();
        if (bccomp($orderbalance, 0) == 0) {
            return ['code' => 1,  'message' => '当前项目不可投,可投余额为0'];
        }
        if (bcdiv($orderbalance, $this->cdeal->start_money) * 1 >= 1) {
            //若可投金额大于起投金额
            if (bcdiv($money, $this->cdeal->start_money) * 1 < 1) {
                return ['code' => self::ERROR_LESS_START_MONEY,  'message' => self::getErrorByCode(self::ERROR_LESS_START_MONEY).'('.$this->cdeal->start_money.'元)'];
            } elseif (bcdiv($orderbalance, $money) * 1 < 1) { //可投金额除以投标金额，如果是小于1的数字，代表超额投资
                return ['code' => self::ERROR_MONEY_MUCH,  'message' => self::getErrorByCode(self::ERROR_MONEY_MUCH)];
            } elseif ($this->cdeal->dizeng_money / 1) {
                bcscale(14);
                $v = bcdiv($money, $this->cdeal->dizeng_money);
                $varr = explode('.', $v);
                if ((bccomp($varr[1], 0)) > 0 &&  bcsub($orderbalance, $money) * 1 != 0) {
                    return ['code' => self::ERROR_DIZENG,  'message' => $this->cdeal->start_money.'元起投,'.$this->cdeal->dizeng_money.'元递增'];
                }
            }
        } else {
            //否则必须投满
            if (bcdiv($orderbalance, $money) * 1 != 1) {
                return ['code' => self::ERROR_MONEY_BALANCE,  'message' => self::getErrorByCode(self::ERROR_MONEY_BALANCE)];
            }
        }
    }

    /**
     * 立即认购时候的限制.
     *
     * @param type $sn
     *
     * @return type
     */
    public function toCart($user = null, $sn = null)
    {
        $commonret = $this->checkCommonCond($user, $sn);
        if ($commonret !== true) {
            return $commonret;
        }

        return ['code' => self::ERROR_SUCCESS,  'message' => '', 'tourl' => '/order/order?sn='.$sn];
    }
}

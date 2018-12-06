<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\user\DrawRecord;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use common\models\draw\DrawManager;
use common\models\draw\DrawException;
use common\models\bank\BankManager;
use common\models\bank\QpayConfig;
use common\models\bank\BankCardUpdate;
use common\models\user\UserFreepwdRecord;
use common\service\BankService;
use common\service\UmpService;
use Yii;
use yii\filters\AccessControl;

class UserbankController extends BaseController
{
    public function behaviors()
    {
        $access = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], //登录用户退出
                    ],
                ],
                'except' => [
                    'refer',
                ],
            ],
        ];

        return $access;
    }

    //Android和iOS的账户中心的原生代码，等APP更新时候重新更新地址，之后此临时转跳可以删除
    public function actionBindbank()
    {
        return $this->redirect('/user/bank');
    }

    //Android和iOS的账户中心的原生代码，等APP更新时候重新更新地址，之后此临时转跳可以删除
    public function actionMycard()
    {
        return $this->redirect('/user/bank/card');
    }

    /**
     * 修改交易密码表单页.
     */
    public function actionEditbuspass()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if (1 === $data['code']) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('editbuspass', ['data' => $data]);
            }
        }

        return $this->render('editbuspass', ['data' => $data]);
    }

    public function actionResetTradePass()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->getAuthedUser(), $cond);
        if (1 === $data['code']) {
            return $data;
        }

        $ump = Yii::$container->get('ump');

        $resp = $ump->resetTradePass($this->getAuthedUser());

        if ($resp->isSuccessful()) {
            return ['code' => 0, 'message' => '重置后的密码已经发送到您的手机'];
        } elseif ($resp->get('ret_code') === '00060031') {
            \Yii::trace('【重置交易密码】'.$this->getAuthedUser()->idcard.':'.$resp->get('ret_code').':'.$resp->get('ret_msg'), 'umplog');

            return ['code' => 1, 'message' => '您的联动账户已被锁定，请联系客服'];
        } else {
            \Yii::trace('【重置交易密码】'.$this->getAuthedUser()->idcard.':'.$resp->get('ret_code').':'.$resp->get('ret_msg'), 'umplog');

            return ['code' => 1, 'message' => '当前网络异常，请稍后重试'];
        }
    }

    /**
     * 快捷支付.
     */
    public function actionRecharge()
    {
        \Yii::$app->session->remove('cfca_qpay_recharge');
        \Yii::$app->session->remove('recharge_back_url');
        $user = $this->getAuthedUser();

        //检查用户是否完成快捷支付
        $data = BankService::checkKuaijie($user);
        if (1 === $data['code']) {
            if (\Yii::$app->request->isAjax) {
                return [
                    'code' => $data['code'],
                    'message' => $data['message'],
                    'next' => $data['tourl']
                ];
            } else {
                if (isset($data['tourl']) && '' !== $data['tourl']) {
                    return $this->redirect($data['tourl']);
                } else {
                    return $this->redirect('/user/user');
                }
            }
        }

        $user_bank = UserBanks::find()->where(['uid' => $user->id])->select('id,binding_sn,bank_id,bank_name,card_number')->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_LEND, 'uid' => $user->id])->select('id,uid,in_sum,available_balance')->one();
        $bank = QpayConfig::findOne($user_bank->bank_id);

        //保存充值来源
        if ($from = Yii::$app->request->get('from')) {
            \Yii::$app->session['recharge_from_url'] = urldecode($from);
        }
        if ($to = Yii::$app->request->get('backUrl')) {
            \Yii::$app->session['recharge_back_url'] = $to;
        }

        return $this->render('recharge', [
            'user_bank' => $user_bank,
            'user_acount' => $user_acount,
            'data' => $data,
            'bank' => $bank,
            'uid' => $user->id,
        ]);
    }

    public function actionRefer()
    {
        return $this->render('refer');
    }

    /**
     * 提现申请表单页.
     */
    public function actionTixian()
    {
        $user = $this->getAuthedUser();
        $uid = $user->id;
        $type = Yii::$app->request->get('type');
        $user_acount = (1=== (int)$type) ? $user->lendAccount : $user->borrowAccount;
        $user_bank = $user->qpay;

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N;
        $data = BankService::check($user, $cond);
        if ($data['code'] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('tixian', ['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data]);
            }
        }

        $draw = new DrawRecord();
        $draw->uid = $uid;
        if ($draw->load(Yii::$app->request->post()) && $draw->validate()) {
            $borrowertype = Yii::$app->request->post('borrowertype');
            $user_acount = (1=== (int)$borrowertype) ? $user->lendAccount : $user->borrowAccount;
            try {
                $drawFee = $user->getDrawCount() >= Yii::$app->params['draw_free_limit'] ? Yii::$app->params['drawFee'] : 0;
                $drawres = DrawManager::initDraw($user_acount, $draw->money, $drawFee);
                if (!$drawres->save()) {
                    throw new \Exception('提现申请失败', '000003');
                }
                $option = array();
                if (null != Yii::$app->request->get('token')) {
                    $option['app_token'] = Yii::$app->request->get('token');
                }
                if(1=== (int)$borrowertype){
                    $next = Yii::$container->get('ump')->initDraw($drawres, null, $option);
                }else{
                    $next = Yii::$container->get('ump')->initBorrowerDraw($drawres, null, $option);
                }

                return ['code' => 0, 'message' => '', 'tourl' => $next];
            } catch (DrawException $ex) {
                if (DrawException::ERROR_CODE_ENOUGH === $ex->getCode()) {
                    return ['code' => 1, 'message' => '您的账户余额不足,仅可提现'.$ex->getMessage().'元', 'money' => $ex->getMessage()];
                } else {
                    return ['code' => 1, 'message' => $ex->getMessage()];
                }
            } catch (\Exception $ex) {
                $draw->addError('money', $ex->getMessage());
            }
        }

        if ($draw->getErrors()) {
            $message = $draw->firstErrors;

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('tixian', [
            'user_bank' => $user_bank,
            'user_acount' => $user_acount,
            'type' => $type,
            'data' => [
                'code' => 0,
                'message' => '',
                'tourl' => '',
            ],
        ]);
    }

    /**
     * 银行限额显示.
     */
    public function actionBankxiane()
    {
        $qpayBanks = QpayConfig::find()->all();

        return $this->render('bankxiane', ['banks' => $qpayBanks]);
    }

    /**
     * 绑卡受理结果页面.
     *
     * @param $ret success/error
     */
    public function actionAccept($ret = 'error')
    {
        $this->layout = '@app/views/layouts/fe';

        return $this->render('acceptres', ['ret' => $ret]);
    }

    /**
     * 快捷充值结果页面.
     *
     * @param type $ret
     */
    public function actionQpayres($ret = 'error')
    {
        $from_url = '';
        if (!Yii::$app->user->isGuest && isset(Yii::$app->session['recharge_from_url'])) {
            $from_url = Yii::$app->session['recharge_from_url'];
            unset(Yii::$app->session['recharge_from_url']);
        }

        return $this->render('qpayres', ['ret' => $ret, 'from_url' => $from_url]);
    }

    /**
     * 提现结果页.
     *
     * @param type $ret
     */
    public function actionDrawres($ret = 'error')
    {
        $time = time();
        $arrivalDate = '';
        $timeStandard = date("Y-m-d 17:00:00", $time);
        if (date("Y-m-d H:i:s", $time) > $timeStandard) {
            if (in_array(date('w', $time), [1, 2, 3, 4])) {
                $arrivalDate = date("Y-m-d ", strtotime("+1days", $time));
            } elseif (in_array(date('w', $time), [5])) {
                $arrivalDate = date("Y-m-d ", strtotime("+3days", $time));
            } elseif (in_array(date('w', $time), [6])) {
                $arrivalDate = date("Y-m-d ", strtotime("+2days", $time));
            } elseif (in_array(date('w', $time), [0])) {
                $arrivalDate = date("Y-m-d ", strtotime("+1days", $time));
            }
        } else {
            if (in_array(date('w', $time), [1, 2, 3, 4, 5])) {
                $arrivalDate = date("Y-m-d ", $time);
            } elseif (in_array(date('w', $time), [0])) {
                $arrivalDate = date("Y-m-d ", strtotime("+1days", $time));
            } elseif (in_array(date('w', $time), [6])) {
                $arrivalDate = date("Y-m-d ", strtotime("+2days", $time));
            }
        }
        $date = date('m-d', strtotime($arrivalDate));
        $finalDate = str_replace('-','月',$date).'日';
        return $this->render('drawres', ['ret' => $ret, 'date' => $finalDate]);
    }

    /**
     * 换卡申请页面.
     */
    public function actionUpdatecard()
    {
        $user = $this->getAuthedUser();

        $data = BankService::checkKuaijie($user);
        if (1 === $data['code']) {
            return $this->redirect('/user/bank/card');
        }

        $userBank = $user->qpay;
        $bankcardUpdate = BankCardUpdate::find()
            ->where(['oldSn' => $userBank->binding_sn, 'uid' => $user->id])
            ->orderBy('id desc')->one();

        if (null !== $bankcardUpdate && BankCardUpdate::STATUS_ACCEPT === $bankcardUpdate->status) {
            return $this->redirect('/user/bank/card');
        }

        $banks = BankManager::getQpayBanks();

        return $this->render('updatecard', ['banklist' => $banks]);
    }

    /**
     * 换卡申请结果页面.
     */
    public function actionUpdatecardnotify($ret = 'error')
    {
        return $this->render('updatecardnotify', ['ret' => $ret]);
    }

    /**
     * 快捷充值商业委托
     */
    public function actionRechargeDeputeWap(){
        //保存目的地
        Yii::$app->session->set('to_url', '/user/userbank/recharge-depute-wap');
        //检查是否开户
        $user = $this->getAuthedUser();
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($user, $cond, true);
        if (1 === $data['code']) {
            if (\Yii::$app->request->isAjax) {
                return [
                    'code' => $data['code'],
                    'message' => $data['message'],
                    'next' => $data['tourl']
                ];
            } else {
                if (isset($data['tourl']) && '' !== $data['tourl']) {
                    return $this->redirect($data['tourl']);
                } else {
                    return $this->redirect('/user/user');
                }
            }
        }

        //检查用户是否开通商业委托免密协议
        $userfree = UserFreepwdRecord::findOne(['uid'=>$user->id]);
        $toOpenMm = [];
        if(empty($userfree)){//未开通
            $toOpenMm = UserFreepwdRecord::getCheckStatusInfo()[0];
        }else if(UserFreepwdRecord::OPEN_FASTPAY_STATUS_PASS !== UserFreepwdRecord::getCheckStatusInfo()[$userfree->status]['code']){
            $toOpenMm  = UserFreepwdRecord::getCheckStatusInfo()[$userfree->status];
        }

        $user_bank = UserBanks::find()->where(['uid' => $user->id])->select('id,binding_sn,bank_id,bank_name,card_number')->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_LEND, 'uid' => $user->id])->select('id,uid,in_sum,available_balance')->one();
        $bank = QpayConfig::findOne($user_bank->bank_id);

        //保存充值来源
        /*if ($from = Yii::$app->request->get('from')) {
            \Yii::$app->session['recharge_from_url'] = urldecode($from);
        }
        if ($to = Yii::$app->request->get('backUrl')) {
            \Yii::$app->session['recharge_back_url'] = $to;
        }*/

        return $this->render('recharge-depute-wap', [
            'user_bank' => $user_bank,
            'user_acount' => $user_acount,
            'data' => $data,
            'bank' => $bank,
            'toOpenMm' => $toOpenMm,
            'uid' => $user->id,
        ]);
    }

    /**
     * 开通快捷支付（商业委托）
     * */
    public function  actionFastpay(){
        $user = $this->getAuthedUser();
        if(null === $user){
            $this->redirect('/site/login');
        }
        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($user, $cond, true);
        if (1 === $data['code']) {
            if (\Yii::$app->request->isAjax) {
                return [
                    'code' => $data['code'],
                    'message' => $data['message'],
                    'next' => $data['tourl']
                ];
            } else {
                if (isset($data['tourl']) && '' !== $data['tourl']) {
                    return $this->redirect($data['tourl']);
                } else {
                    return $this->redirect('/user/user');
                }
            }
        }
        $epayUserId = $user->epayUser->epayUserId;
        $oneinfo = UserFreepwdRecord::findOne(['uid'=> $user->id]);
        if(empty($oneinfo)){
            $model = new UserFreepwdRecord();
            $model->uid = $user->id;
            $model->status = UserFreepwdRecord::OPEN_FREE_STATUS_WAIT;
            $model->epayUserId = $epayUserId;
            $model->save(false);
        }
        $this->redirect(\Yii::$container->get('ump')->openFastPay($epayUserId));
    }

    /**
     * 开通免密充值（商业委托）
     * */
    public function  actionFreeRecharge(){
        $user = $this->getAuthedUser();
        if(null === $user){
            $this->redirect('/site/login');
        }
        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($user, $cond, true);
        if (1 === $data['code']) {
            if (\Yii::$app->request->isAjax) {
                return [
                    'code' => $data['code'],
                    'message' => $data['message'],
                    'next' => $data['tourl']
                ];
            } else {
                if (isset($data['tourl']) && '' !== $data['tourl']) {
                    return $this->redirect($data['tourl']);
                } else {
                    return $this->redirect('/user/user');
                }
            }
        }
        $this->redirect(Yii::$container->get('ump')->openFreeRecharge($user->epayUser->epayUserId));
    }

    /**
     * 用户开通免密协议
     */
    public function actionOpenfree(){
        $user = $this->getAuthedUser();
        if(null === $user){
            $this->redirect('/site/login');
        }
        if(empty($user->mianmiStatus)){
            $next = Yii::$container->get('ump')->openmianmi($user->epayUser->epayUserId);
        }else{
            $next = 'deal/deal/index';
        }
        $this->redirect($next);
    }
}

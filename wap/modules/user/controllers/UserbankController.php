<?php

namespace app\modules\user\controllers;

use Yii;
use yii\web\Response;
use app\controllers\BaseController;
use common\models\user\DrawRecord;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use common\service\BankService;
use common\models\draw\DrawManager;
use common\models\draw\DrawException;
use common\models\bank\BankManager;
use common\models\bank\QpayConfig;

class UserbankController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->layout = '@app/modules/order/views/layouts/buy';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    /**
     * 实名认证表单页.
     */
    public function actionIdcardrz()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_Y;
        $data = BankService::check($this->user, $cond);
        if ($data['code'] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('idcardrz', $data);
            }
        }

        $model = $this->user;
        $model->scenario = 'idcardrz';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $umpService = new \common\service\UmpService();
            try {
                $umpService->register($model);
                return ['tourl' => '/user/userbank/rzres?ret=success', 'code' => 0, 'message' => '您已成功开户'];
            } catch (\Exception $ex) {
                return ['code' => 1, 'message' => $ex->getMessage()];
            }
        }

        if ($model->getErrors()) {
            $err = $model->getSingleError();
            return ['code' => 1, 'message' => $err['message']];
        }

        return $this->render('idcardrz');
    }

    /**
     * 绑定银行卡表单页.
     */
    public function actionBindbank()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::MIANMI_VALIDATE | BankService::BINDBANK_VALIDATE_Y;
        $data = BankService::check($this->user, $cond);
        if ($data['code'] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                $arr = array();
                return $this->render('bindbank', ['banklist' => $arr, 'data' => $data]);
            }
        }

        $banks = BankManager::getQpayBanks();
        return $this->render('bindbank', ['banklist' => $banks]);
    }

    /**
     * 修改交易密码表单页.
     */
    public function actionEditbuspass()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->user, $cond);
        if (1 === $data['code']) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('editbuspass', ['data' => $data]);
            }
        }

        return $this->render('editbuspass');
    }

    public function actionResetTradePass()
    {
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->user, $cond);
        if (1 === $data['code']) {
            return $data;
        }

        $ump = Yii::$container->get('ump');

        $resp = $ump->resetTradePass($this->user);

        if ($resp->isSuccessful()) {
            return ['code' => 0, 'message' => '重置后的密码已经发送到您的手机'];
        } else {
            \Yii::trace('【重置交易密码】'.$this->user->idcard.":".$resp->get('ret_code').":".$resp->get('ret_msg'), 'umplog');
            return ['code' => 1, 'message' => '当前网络异常，请稍后重试'];
        }
    }

    /**
     * 快捷支付.
     */
    public function actionRecharge()
    {
        \Yii::$app->session->remove('cfca_qpay_recharge');
        $user = $this->user;
        $uid = $user->id;
        $user_bank = UserBanks::find()->where(['uid' => $uid])->select('id,binding_sn,bank_id,bank_name,card_number')->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_LEND, 'uid' => $uid])->select('id,uid,in_sum,available_balance')->one();
        $bank = QpayConfig::findOne($user_bank->bank_id);
        //检查用户是否完成快捷支付
        $data = BankService::checkKuaijie($user);
        if ($data[code] == 1 && \Yii::$app->request->isAjax) {
            return ['next' => $data['tourl']];
        }
        //保存充值来源
        if ($from = Yii::$app->request->get('from')) {
            Yii::$app->session['recharge_from_url'] = urldecode($from);
        }
        return $this->render('recharge', ['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data, 'bank' => $bank]);
    }

    /**
     * 提现申请表单页.
     */
    public function actionTixian()
    {
        $user = $this->user;
        $uid = $user->id;

        $user_acount = $user->lendAccount;
        $user_bank = $user->qpay;

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N;
        $data = BankService::check($user, $cond);
        if ($data[code] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('tixian', ['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data]);
            }
        }

        $draw = new DrawRecord();
        $draw->uid = $uid;
        if ($draw->load(Yii::$app->request->post()) && $draw->validate()) {
            try {
                $drawres = DrawManager::initDraw($user_acount, $draw->money, \Yii::$app->params['drawFee']);
                $next = Yii::$container->get('ump')->initDraw($drawres);
                return ['code' => 0, 'message' => '', 'tourl' => $next];
            } catch (DrawException $ex) {
                if (DrawException::ERROR_CODE_ENOUGH === $ex->getCode()) {
                    return ['code' => 1, 'message' => '您的账户余额不足,仅可提现' . $ex->getMessage() . '元', 'money' => $ex->getMessage()];
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

        return $this->render('tixian', ['user_bank' => $user_bank, 'user_acount' => $user_acount]);
    }

    /**
     * 检查银行卡号，返回开户行名称.
     */
    public function actionCheckbank()
    {
        return BankService::checkBankcard(Yii::$app->request->post('card'));
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
     * 绑卡受理结果页面
     * @param $ret success/error
     */
    public function actionAccept($ret = 'error')
    {
        $this->layout = '@app/modules/user/views/layouts/notify';
        return $this->render('acceptres', ['ret' => $ret]);
    }

    /**
     * 快捷充值结果页面
     * @param type $ret
     */
    public function actionQpayres($ret = 'error')
    {
        $from_url = '';
        if (!Yii::$app->user->isGuest && isset(Yii::$app->session['recharge_from_url'])) {
            $from_url = Yii::$app->session['recharge_from_url'];
            unset(Yii::$app->session['recharge_from_url']);
        }
        $this->layout = '@app/modules/user/views/layouts/notify';
        return $this->render('qpayres', ['ret' => $ret, 'from_url' => $from_url]);
    }

    /**
     * 提现结果页
     * @param type $ret
     */
    public function actionDrawres($ret = 'error')
    {
        $this->layout = '@app/modules/user/views/layouts/notify';
        return $this->render('drawres', ['ret' => $ret]);
    }

    /**
     * 开户结果页
     * @param type $ret
     */
    public function actionRzres($ret = 'error')
    {
        $this->layout = '@app/modules/user/views/layouts/notify';
        return $this->render('rzres', ['ret' => $ret]);
    }
}

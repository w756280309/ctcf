<?php

namespace frontend\modules\user\controllers;

use Yii;
use frontend\controllers\BaseController;
use common\models\user\RechargeRecord;
use common\service\BankService;
use common\models\bank\BankManager;
use common\utils\TxUtils;
use common\models\bank\EbankConfig;
use yii\filters\AccessControl;

class RechargeController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    /**
     * 充值
     */
    public function actionInit()
    {
        $this->layout = 'main';
        Yii::$app->session->set('to_url', '/user/recharge/init');

        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->user, $cond);
        if (1 === $data['code']) {
            return $this->render('@frontend/modules/user/views/userbank/identity.php', [
                'title' => '充值',
            ]);
        }

        $bank = BankManager::getEbank('personal');

        $recharge = new RechargeRecord();
        $user_account = $this->user->lendAccount;

        //充值成功跳转url
        $url = '/user/user/index';

        //检查是否开通免密
        $cond = 0 | BankService::MIANMI_VALIDATE_N;
        $data = BankService::check($this->user, $cond);

        return $this->render('recharge', [
            'recharge' => $recharge,
            'user_account' => $user_account,
            'bank' => $bank,
            'url' => $url,
            'data' => $data,
            'user' => $this->user,
        ]);
    }

    /**
     * 充值表单提交一面
     */
    public function actionApply()
    {

        //检查是否开户
        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N;
        $data = BankService::check($this->user, $cond);
        if (1 === $data['code']) {
            return $this->redirect('/user/userbank/identity');
        }

        $bank_id = Yii::$app->request->post('bankid');
        $pay_type = Yii::$app->request->post('pay_type');   //目前只支持网银充值 2

        if (empty($bank_id) || empty($pay_type)) {
            throw new \Exception('The argument bank_id or pay_type is null.');
        }

        $recharge = new RechargeRecord([
            'sn' => TxUtils::generateSn("RC"),
            'pay_type' => $pay_type,
            'pay_id' => 0,
            'account_id' => $this->user->lendAccount->id,
            'uid' => $this->user->id,
            'bank_id' => $bank_id, //网银充值,不要求绑卡,存储的是银行的统一ID
            'pay_bank_id' => $bank_id, //网银充值,不要求绑卡,存储的是银行的统一ID
            'status' => RechargeRecord::STATUS_NO,
            'clientIp' => ip2long(Yii::$app->request->userIP),
            'epayUserId' => $this->user->epayUser->epayUserId,
        ]);

        if ($recharge->load(Yii::$app->request->post()) && $recharge->validate()) {
            //录入recharge_record记录
            if (!$recharge->save(false)) {
                return $this->redirect('/info/fail?source=chongzhi&jumpUrl=/user/recharge/init');
            }

            // 设置session。用来验证数据的不可修改
            Yii::$app->session->set('epayLend_brecharge', [
                'recharge_sn' => $recharge->sn,
            ]);

            $ump = Yii::$container->get('ump');
            $bank = EbankConfig::findOne(['bankId' => $bank_id]);

            $ump->rechargeViaBpay($recharge, $bank->bank->gateId);
        } else {
            return $this->redirect('/info/fail?source=chongzhi&jumpUrl=/user/recharge/init');
        }
    }
}

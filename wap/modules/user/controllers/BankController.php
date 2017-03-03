<?php
/**
 * Created by ShiYang.
 * Date: 17-1-6
 * Time: 下午1:45
 */

namespace app\modules\user\controllers;


use app\controllers\BaseController;
use common\action\user\BankCheckAction;
use common\action\user\BankUpdateAction;
use common\action\user\BankUpdateVerifyAction;
use common\action\user\BankVerifyAction;
use common\models\bank\BankCardUpdate;
use common\models\bank\BankManager;
use common\service\BankService;

class BankController extends BaseController
{
    public function actions()
    {
        return [
            'check' => BankCheckAction::className(),//根据卡号匹配开户行
            'verify' => BankVerifyAction::className(),//绑卡表单提交页面
            'update' => BankUpdateAction::className(),//换卡页面
            'update-verify' => BankUpdateVerifyAction::className(),//换卡表单提交页面
        ];
    }

    /**
     * 绑定银行卡页面.
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/fe';

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::MIANMI_VALIDATE_N | BankService::BINDBANK_VALIDATE_Y;
        $data = BankService::check($this->getAuthedUser(), $cond);
        $banks = BankManager::getQpayBanks();

        return $this->render('index', [
            'banklist' => $banks,
            'data' => $data,
        ]);
    }

    //我的银行卡
    public function actionCard()
    {
        $user = $this->getAuthedUser();
        $data = BankService::checkKuaijie($user);
        if (1 === $data['code']) {
            if ($data['tourl']) {
                return $this->redirect($data['tourl']);
            } else {
                return $this->goHome();
            }
        }

        $userBank = $user->qpay;
        $bankcardUpdate = BankCardUpdate::find()
            ->where(['oldSn' => $userBank->binding_sn, 'uid' => $user->id])
            ->orderBy('id desc')->one();

        if (null !== $bankcardUpdate && BankCardUpdate::STATUS_ACCEPT !== $bankcardUpdate->status) {
            $bankcardUpdate = null;
        }

        return $this->render('card', [
            'userBank' => $userBank,
            'bankcardUpdate' => $bankcardUpdate
        ]);
    }
}
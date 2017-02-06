<?php
/**
 * Created by ShiYang.
 * Date: 17-1-9
 * Time: 上午10:39
 */

namespace common\action\user;

//换卡页面action
use common\models\bank\BankCardUpdate;
use common\models\bank\BankManager;
use common\service\BankService;
use yii\base\Action;

class BankUpdateAction extends Action
{
    public function run()
    {
        $user = $this->controller->getAuthedUser();

        $data = BankService::checkKuaijie($user);
        if ($data['code']) {
            return $this->controller->redirect('/user/bank/card');
        }

        $userBank = $user->qpay;
        $bankcardUpdate = BankCardUpdate::find()
            ->where(['oldSn' => $userBank->binding_sn, 'uid' => $user->id])
            ->orderBy('id desc')->one();

        if ($bankcardUpdate && BankCardUpdate::STATUS_ACCEPT === $bankcardUpdate->status) {
            return $this->controller->redirect('/user/bank/card');
        }

        $banks = BankManager::getQpayBanks();

        return $this->controller->render('update', ['banklist' => $banks]);
    }
}
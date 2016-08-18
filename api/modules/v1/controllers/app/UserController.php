<?php

namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;
use common\models\app\AccessToken;
use common\models\bank\BankCardUpdate;

/**
 * App相关api接口.
 */
class UserController extends Controller
{
    /**
     * 获取用户信息.
     */
    public function actionInfo($token)
    {
        if (empty($token)) {
            return [
                'status' => 'fail', //程序级别成功失败
                'message' => '需要登录',
                'data' => null,
            ];
        }

        $accessToken = AccessToken::findOne(['token' => $token]);

        if (null === $accessToken) {
            return [
                'status' => 'fail', //程序级别成功失败
                'message' => '需要登录',
                'data' => null,
            ];
        }

        $user = $accessToken->user;
        if (!$user) {
            return [
                'status' => 'fail', //程序级别成功失败
                'message' => '找不到用户',
                'data' => null,
            ];
        }

        $bank = $user->qpay;
        $cardUpdateFlag = false;

        if (null !== $bank) {
            $bankCard = substr_replace($bank->card_number, '*****', 3, -2);

            $bankcardUpdate = BankCardUpdate::find()
                ->where(['oldSn' => $bank->binding_sn, 'uid' => $user->id])
                ->orderBy('id desc')->one();

            if ($bankcardUpdate && BankCardUpdate::STATUS_ACCEPT === $bankcardUpdate->status) {
                $cardUpdateFlag = true;
            }
        } else {
            $bankCard = null;
        }

        $ua = $user->lendAccount;
        if (null === $ua) {
            return [
                'status' => 'fail', //程序级别成功失败
                'message' => '找不到账户',
                'data' => null,
            ];
        }

        return [
            'status' => 'success', //程序级别成功失败
            'message' => '成功',
            'data' => [
                'result' => 'success', //业务级别成功失败
                'msg' => '成功',
                'content' => [
                    'user_id' => $user->id,
                    'asset_total' => strval($ua->getTotalFund()),
                    'profit_balance' => strval($ua->getTotalProfit()),
                    'investment_balance' => strval(bcadd($ua->investment_balance, $ua->freeze_balance, 2)),
                    'available_balance' => strval($ua->available_balance),
                    'mobile' => substr_replace($user->mobile, '***', 3, -4),
                    'idcard' => empty($user->idcard) ? null : substr_replace($user->idcard, '***', 5, -2),
                    'bankcard' => $bankCard,
                    'bankcard_update_pending' => $cardUpdateFlag,
                ],
            ],
        ];
    }
}

<?php

namespace app\modules\user\controllers\qpay;

use common\models\user\User;
use Ding\DingNotify;
use Yii;
use Exception;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use common\models\user\RechargeRecord;
use common\service\AccountService;
use common\models\TradeLog;
use common\models\epay\EpayUser;
use common\models\user\UserAccount;

/**
 * 绑卡回调控制器4.2.
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class QpaynotifyController extends Controller
{
    /**
     * 快捷充值前台通知地址
     */
    public function actionFrontend()
    {
        $data = Yii::$app->request->get();
        $ret = $this->processing($data);
        if ($ret instanceof RechargeRecord) {
            return $this->redirect('/user/userbank/qpayres?ret=success');
        }

        return $this->redirect('/user/userbank/qpayres');
    }

    /**
     * 快捷充值后台通知地址
     */
    public function actionBackend()
    {
        $this->layout = false;
        $err = '00009999';
        $errmsg = 'no error';
        $data = Yii::$app->request->get();
        $ret = $this->processing($data);
        if ($ret instanceof RechargeRecord) {
            $err = '0000';
        } else {
            $errmsg = '异常';
        }
        $content = Yii::$container->get('ump')->buildQuery([
            'order_id' => $data['order_id'],
            'mer_date' => $data['mer_date'],
            'reg_code' => $err,
        ]);

        return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
    }

    /**
     * @param array $data
     *                    说明 data 中 amount，user_id，account_id，mobile_id是判断pos充值的依据
     *
     * @return type
     *
     * @throws NotFoundHttpException
     * @throws Exception
     */
    private function processing(array $data = [])
    {
        TradeLog::initLog(2, $data, $data['sign'])->save();
        if (array_key_exists('token', $data)) {
            unset($data['token']);
        }
        $recharge = RechargeRecord::findOne(['sn' => $data['order_id']]);
        $epayUser = EpayUser::findOne(['epayUserId' => $recharge->uid]);
        if (
            Yii::$container->get('ump')->verifySign($data)
            && '0000' === $data['ret_code']
            && 'recharge_notify' === $data['service']
        ) {
            $rc = null;
            if (
                array_key_exists('amount', $data) && null !== $data['amount']
                && array_key_exists('user_id', $data) && null !== $data['user_id']
                && array_key_exists('account_id', $data) && null !== $data['account_id']
                && array_key_exists('mobile_id', $data) && null !== $data['mobile_id']
            ) {
                if (null === $epayUser) {
                    throw new Exception($data['user_id'].'此用户不存在');
                }

                if ($recharge) {
                    throw new Exception($data['order_id'].'线下充值已成功');
                }
                $ua = UserAccount::findOne(['uid' => $epayUser->appUserId]);
                $rc = new RechargeRecord([
                    'sn' => $data['order_id'],
                    'uid' => $epayUser->appUserId,
                    'fund' => $data['amount'] / 100,
                    'account_id' => $ua->id, ///待定
                    'bank_id' => '0',
                    'pay_bank_id' => '0',
                    'pay_type' => RechargeRecord::PAY_TYPE_POS,
                    'clientIp' => ip2long(Yii::$app->request->userIP),
                    'epayUserId' => $epayUser->epayUserId,
                    'status' => 0,
                ]);
                if (!$rc->validate()) {
                    throw new Exception($data['order_id'].'充值失败:'.$rc->getSingleError()['message']);
                }
                $rc->save(false);
            } else {
                $rc = RechargeRecord::findOne(['sn' => $data['order_id']]);
            }
            if (null !== $rc) {
                $acc_ser = new AccountService();
                $is_success = $acc_ser->confirmRecharge($rc);
                if ($is_success) {
                    return $rc;
                } else {
                    throw new Exception($data['order_id'].'充值失败');
                }
            } else {
                throw new NotFoundHttpException($data['order_id'].':无法找到申请数据');
            }
        } else {
            $user = User::findOne($epayUser->appUserId);
            if (!empty($user)) {
                (new DingNotify('wdjf'))->sendToUsers('用户[' . $user->mobile . ']，于' . date('Y-m-d H:i:s') . ' 进行快捷充值操作，操作失败，联动充值失败，失败信息:' . $data['ret_msg']);
            }
            throw new Exception($data['order_id'].'处理失败');
        }
    }
}

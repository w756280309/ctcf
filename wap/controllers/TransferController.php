<?php

namespace app\controllers;

use common\models\thirdparty\Channel;
use common\models\transfer\TransferTx;
use common\models\user\User;
use common\service\AccountService;
use common\utils\TxUtils;
use Njq\Crypto;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class TransferController extends Controller
{
    /**
     * 参数：
     *
     * - 金额（money）
     * - 南金中心当前用户ID（userId）
     * - 业务流水号（sn）
     * - 平台ID platformId
     * - 版本号 version
     * - 请求时间 requestTime
     * - 签名（sign）
     *
     * @return string|\yii\web\Response
     * @throws \Exception
     */
    public function actionAuth()
    {
        $request = Yii::$app->request;
        $params = $request->get();
        $money = (string) $request->get('money');
        $userId = (int) $request->get('userId');
        $sn = trim($request->get('sn'));

        //验签
        $crypto = new Crypto();
        if (!$crypto->verifySign($params) || '' === $sn) {
            return $this->render('index', [
                'code' => 2001,
                'message' => '金额格式不正确',
            ]);
        }

        //验证金额格式
        if (!preg_match('/^[0-9]+(\.[0-9]+)?$/', $money)) {
            return $this->render('index', [
                'code' => 2002,
                'message' => '金额格式不正确',
            ]);
        }

        //验证账户
        $channel = Channel::find()
            ->where(['thirdPartyUser_id' => $userId])
            ->one();
        if (null === $channel) {
            return $this->render('index', [
                'code' => 2003,
                'message' => '温都金服账户连接超时',
            ]);
        }

        $user = User::findOne($channel->userId);
        if (!(null !== $user
            && null !== ($epayUser = $user->epayUser))
        ) {
            return $this->render('index', [
                'code' => 2004,
                    'message' => '温都金服账户连接超时',
            ]);
        }

        //验证账户余额
        $userAccount = $user->lendAccount;
        if (null === $userAccount
            || -1 === bccomp($userAccount->available_balance, $money, 2)
        ) {
            return $this->render('index', [
                'code' => 2005,
                'message' => '温都金服账户余额不足',
            ]);
        }

        //添加授权转账业务申请记录
        try {
            $transferTx = TransferTx::find()
                ->where(['ref_sn' => $sn])
                ->one();
            if (null !== $transferTx) {
                throw new \Exception('订单号已生成');
            }

            $transferTx = new TransferTx([
                'sn' => TxUtils::generateSn('UTP'),
                'ref_sn' => $sn,
                'userId' => $user->id,
                'money' => $money,
            ]);
            $transferTx->save(false);
        } catch (\Exception $ex) {
            return $this->render('index', [
                'code' => 2006,
                'message' => '订单生成超时',
            ]);
        }

        $epayUserId = $epayUser->epayUserId;
        $retUrl = Yii::$app->request->hostInfo . '/transfer/frontend';
        $notifyUrl = Yii::$app->request->hostInfo . '/transfer/backend';
        $ump = Yii::$container->get('ump');
        $url = $ump->userToPlatform($epayUserId, $money, $retUrl, $notifyUrl, $transferTx->sn, true);

        return $this->redirect($url);
    }

    /**
     * 授权转账后台回调
     *
     * //todo 当前只将初始的订单改为处理中
     */
    public function actionBackend()
    {
        $ump = Yii::$container->get('ump');
        $data = Yii::$app->request->get();
        if (array_key_exists('token', $data)) {
            unset($data['token']);
        }
        if ($ump->verifySign($data)
            && '0000' === $data['ret_code']
            && 'transfer_notify' === $data['service']
        ) {
            $transferTx = TransferTx::find()
                ->where(['sn' => $data['order_id']])
                ->one();
            if (null === $transferTx) {
                throw new BadRequestHttpException('订单不存在');
            }

            $db = Yii::$app->db;
            //当前订单为初始状态时，更新状态为处理中
            if (0 === $transferTx->status) {
                //转账订单变为处理中
                $sql = "update transfer_tx set status = 1 where status = 0 and sn=:sn limit 1";
                $db->createCommand($sql, [
                    'sn' => $data['order_id'],
                ])->execute();
            }

            $content = $ump->buildQuery([
                'order_id' => $data['order_id'],
                'mer_date' => $data['mer_date'],
                'reg_code' => '0000',
                'version' => '4.0',
            ]);

            return $this->render('@borrower/modules/user/views/recharge/recharge_notify.php', ['content' => $content]);
        }
    }

    /**
     * 授权转账前台回调
     */
    public function actionFrontend()
    {
        $ump = Yii::$container->get('ump');
        $data = Yii::$app->request->get();
        if (array_key_exists('token', $data)) {
            unset($data['token']);
        }
        if ($ump->verifySign($data)
            && '0000' === $data['ret_code']
            && 'transfer_notify' === $data['service']
        ) {
            $transferTx = TransferTx::find()
                ->where(['sn' => $data['order_id']])
                ->one();
            if (null === $transferTx) {
                throw new BadRequestHttpException('订单不存在');
            }

            //主动查询订单是否成功
            $transferInfo = $ump->getTransferInfo($data['order_id'], $data['mer_date']);
            //判断订单是否成功
            if ($transferInfo->isSuccessful() && '2' === $transferInfo ->get('tran_state')) {
                //确认授权转账成功处理逻辑
                $accountService = new AccountService();
                $accountService->confirmTransfer($transferTx);
                //获得南金中心的认购处理中url
                $crypto = new Crypto();
                $param['sn'] = $transferTx->ref_sn;
                $params = $crypto->sign($param);
                $url = Yii::$app->params['njq']['host_m'] . 'order/wdjf-order/result?' . http_build_query($params);

                return $this->redirect($url);
            }
        } else {
            throw new BadRequestHttpException('非法请求');
        }
    }
}

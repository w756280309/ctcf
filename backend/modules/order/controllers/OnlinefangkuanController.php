<?php

namespace backend\modules\order\controllers;

use backend\controllers\BaseController;
use backend\modules\order\core\FkCore;
use backend\modules\order\service\FkService;
use common\lib\err\Err;
use common\models\draw\DrawManager;
use common\models\order\OnlineFangkuan;
use common\models\product\OnlineProduct;
use common\models\user\User;
use common\models\user\UserAccount;
use common\utils\TxUtils;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OnlinefangkuanController extends BaseController
{
    /**
     * 放款审核界面.
     */
    public function actionExaminfk($pid)
    {
        $this->layout = false;
        if (empty($pid)) {
            throw $this->ex404();  //参数无效时,返回404错误
        }

        $deal = OnlineProduct::findOne($pid);
        $financing_user = User::findOne(['id' => $deal->borrow_uid]);
        if (null === $deal || null === $financing_user) {    //当数据库中没有标的和融资人信息时,抛出404错误
            throw $this->ex404();  //参数无效时,返回404错误
        }

        return $this->render('examinfk', ['deal' => $deal, 'borrow_user' => $financing_user]);
    }

    /**
     * 放款审核.
     */
    public function actionCheckfk()
    {
        $pid = Yii::$app->request->post('pid');
        $status = Yii::$app->request->post('status');

        $fkservice = new FkService();
        $fs = $fkservice->examinFk($pid, $this->admin_id);

        if ($fs !== true) {
            return $fs;
        }

        $fkcore = new FkCore();
        $ret = $fkcore->createFk($this->admin_id, $pid, $status);

        return $ret;
    }

    /**
     * 融资会员提现.
     */
    public static function actionInit($pid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (empty($pid)) {
            throw new NotFoundHttpException();  //参数无效时,返回404错误
        }

        $onlineProduct = OnlineProduct::findOne($pid);
        if (!$onlineProduct) {
            return ['res' => 0, 'msg' => self::code('000002').'标的信息不存在'];
        }

        $onlineFangkuan = OnlineFangkuan::findOne(['online_product_id' => $pid]);
        if (!$onlineFangkuan) {
            return ['res' => 0, 'msg' => self::code('000002').'放款记录不存在'];
        }

        if (!self::allowDraw($onlineFangkuan)) {
            return ['res' => 0, 'msg' => self::code('000006').'当前放款状态不允许提现操作'];
        }

        $user = $onlineProduct->getFangKuanUser();
        if (null === $user) {
            return ['res' => 0, 'msg' => self::code('000002').'放款用户信息不存在'];
        }
        $account = $user->borrowAccount;
        if (null === $account) {
            return ['res' => 0, 'msg' => self::code('000002').'放款账户信息不存在'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            //融资方放款,不收取手续费
            $draw = DrawManager::initDraw($account, $onlineFangkuan->order_money);
            if (!$draw->save()) {
                throw new \Exception('提现申请失败', '000003');
            }

            $draw->orderSn = $onlineFangkuan->sn;
            if (!$draw->save()) {
                throw new \Exception('写入放款流水失败', '000003');
            }

            $ump = Yii::$container->get('ump');
            //当不允许访问联动时候，默认联动测处理成功
            if (Yii::$app->params['ump_uat']) {
                $borrowerInfo = $user->borrowerInfo;
                $isPersonal = $borrowerInfo->isPersonal();
                if ($isPersonal) {
                    $resp = $ump->orgDrawNoPass(TxUtils::generateSn('DRP'), date('Ymd'), $user->epayUser->epayUserId, $onlineFangkuan->order_money, Yii::$app->request->hostInfo.'/order/drawnotify/notify');
                } else {
                    $resp = $ump->orgDrawApply($draw);
                }
                if (!$resp->isSuccessful()) {
                    throw new \Exception($resp->get('ret_code').$resp->get('ret_msg'));
                }
            }

            $onlineFangkuan->status = OnlineFangkuan::STATUS_TIXIAN_APPLY;
            if (!$onlineFangkuan->save()) {
                throw new \Exception('修改放款审核状态失败', '000003');
            }

            DrawManager::ackDraw($draw);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $code = $e->getCode() ? self::code($e->getCode()) : '';

            return ['res' => 0, 'msg' => $code.$e->getMessage()];
        }

        return ['res' => 1, 'msg' => '提现申请成功'];
    }

    private static function allowDraw(OnlineFangkuan $fangkuan)
    {
        return in_array($fangkuan->status, [
            OnlineFangkuan::STATUS_FANGKUAN,
            OnlineFangkuan::STATUS_TIXIAN_FAIL,
        ]);
    }

    private static function code($code)
    {
        return '['.Err::code($code).']';
    }
}

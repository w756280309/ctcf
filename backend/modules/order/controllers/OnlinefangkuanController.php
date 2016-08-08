<?php

namespace backend\modules\order\controllers;

use Yii;
use common\models\user\User;
use common\models\product\OnlineProduct;
use backend\controllers\BaseController;
use common\models\order\OnlineFangkuan;
use backend\modules\order\service\FkService;
use backend\modules\order\core\FkCore;
use common\models\user\UserAccount;
use common\models\draw\DrawManager;
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
        $financing_user = User::findOne(['type' => User::USER_TYPE_ORG, 'id' => $deal->borrow_uid]);
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
        Yii::$app->response->format = Response::FORMAT_JSON;
        bcscale(14);
        $fkservice = new FkService();
        $pid = Yii::$app->request->post('pid');
        $status = Yii::$app->request->post('status');
        $fs = $fkservice->examinFk($pid, Yii::$app->user->id);
        if ($fs !== true) {
            return $fs;
        }

        $fkcore = new FkCore();
        $ret = $fkcore->createFk(Yii::$app->user->id, $pid, $status);

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
            return ['res' => 0, 'msg' => '标的信息不存在'];
        }

        $onlineFangkuan = OnlineFangkuan::findOne(['online_product_id' => $pid]);

        if (!$onlineFangkuan) {
            return ['res' => 0, 'msg' => '放款记录不存在'];
        }

        if (OnlineFangkuan::STATUS_FANGKUAN !== $onlineFangkuan->status) {
            return ['res' => 0, 'msg' => '当前放款状态不允许提现操作'];
        }

        $account = UserAccount::findOne(['uid' => $onlineFangkuan->uid, 'type' => UserAccount::TYPE_BORROW]);
        if (!$account) {
            return ['res' => 0, 'msg' => 'The borrower account info is not existed.'];
        }

        //融资方放款,不收取手续费
        $draw = DrawManager::initDraw($account, $onlineFangkuan->order_money);
        if (!$draw) {
            return ['res' => 0, 'msg' => '提现申请失败'];
        }

        $draw->orderSn = $onlineFangkuan->sn;
        if (!$draw->save()) {
            return ['res' => 0, 'msg' => '写入放款流水失败'];
        }

        $ump = Yii::$container->get('ump');
        //当不允许访问联动时候，默认联动测处理成功
        if (Yii::$app->params['ump_uat']) {
            $resp = $ump->orgDrawApply($draw);
            if (!$resp->isSuccessful()) {
                return ['res' => 0, 'msg' => $resp->get('ret_code') . $resp->get('ret_msg')];
            }
        }
        DrawManager::ackDraw($draw);
        $onlineFangkuan->status = OnlineFangkuan::STATUS_TIXIAN_APPLY;
        if (!$onlineFangkuan->save()) {
            return ['res' => 0, 'msg' => '修改放款审核状态失败'];
        }

        return ['res' => 1, 'msg' => '提现申请成功'];
    }
}

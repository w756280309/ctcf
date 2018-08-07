<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-5-26
 * Time: 下午1:36
 */
namespace api\modules\asset\controllers;

use common\models\epay\EpayUser;
use common\models\product\Asset;
use common\models\user\User;
use common\utils\SecurityUtils;
use yii\web\Controller;
use Yii;

class AssetController extends Controller
{
    //接收小微资产推送的资产包
    public function actionCreate()
    {
        Yii::$app->response->format = 'json';
        if (!Yii::$app->request->isPost) {
            return ['code' => 300, 'message' => '非法的请求方式'];
        }
        $post = Yii::$app->request->post();
        //是否可发标
        if (1 != trim($post['issue'])) {
            return ['code' => 300, 'message' => '资产方商务协议等未准备就绪，暂不具备推送和上标条件'];
        }
        try {
            $sign = $post['sign'];
            //签名秘钥
            $signKey = Yii::$app->params['zrx']['signKey'];
            $res = Asset::sign($post, $signKey);
            if ($res == $sign) {    //验签成功
                //判断融资用户是否已经开户
                $user = User::findOne([
                    'safeIdCard' => SecurityUtils::encrypt(trim($post['loanUserIdcard'])),
                    'type' => User::USER_TYPE_ORG,
                    'status' => User::STATUS_ACTIVE,
                    'is_soft_deleted' => 0,
                ]);
                if (is_null($user)) {

                    return ['code' => 300, 'message' => '未开户'];
                }
                //保存资产包数据
                $model = Asset::initNew($post);
                if ($model->validate() && $model->save()) {

                    return ['code' => 200, 'message' => '成功'];
                } else {
                    Yii::info('资产包['.$post['sn'].']请求失败，失败原因：'.json_encode($model->getErrors()));

                    return ['code' => 300, 'message' => $model->getErrors()];
                }
            } else {

                return ['code' => 300, 'message' => '验签失败'];
            }
        } catch (\Exception $e) {
            Yii::info('资产包接收失败：' . $e->getMessage());

            return ['code' => 301, 'message' => '接口程序内部错误'];
        }
    }

    /**
     * 静默开户
     * 异常：请求方法不正确；验签失败；开户失败
     * @return array
     */
    public function actionRegister()
    {
        Yii::$app->response->format = 'json';
        //验证求求方法（必须是post请求）
        if (!Yii::$app->request->isPost) {
            return ['code' => 300, 'message' => '非法的请求方式'];
        }

        //接收请求数据
        $post = Yii::$app->request->post();

        try {
            $sign = $post['sign'];
            //签名秘钥
            $signKey = Yii::$app->params['zrx']['signKey'];
            $res = Asset::sign($post, $signKey);
            if ($res == $sign) {    //验签成功
                //判断融资用户是否已经开户
                $user = User::findOne([
                    'safeIdCard' => SecurityUtils::encrypt(trim($post['IdNo'])),
                    'type' => User::USER_TYPE_ORG,
                    'status' => User::STATUS_ACTIVE,
                    'is_soft_deleted' => 0,
                ]);
                if (!is_null($user)) {

                    return ['code' => 0, 'message' => '已开户'];
                } else {
                    //未开户进行开户操作
                    try {
                        //渠道是慧釜的资产，个人融资方允许做为收款方
                        $allowDisbursement = trim($post['source']) == 'HF0001';
                        User::createOrgUser($post['phone'], $post['IdNo'], $post['UsrName'], $allowDisbursement);

                        return ['code' => 0, 'message' => '开户成功'];
                    } catch (\Exception $ex) {
                        Yii::info('个人融资用户开户失败：' . $ex->getMessage());

                        return ['code' => 300, 'message' => '开户失败,' . $ex->getMessage()];
                    }
                }
            } else {

                return ['code' => 300, 'message' => '验签失败'];
            }
        } catch (\Exception $e) {
            Yii::info('静默开户失败：' . $e->getMessage());

            return ['code' => 301, 'message' => '接口程序内部错误'];
        }
    }

    /*
     * 小微请求资金方获取开户用户名等信息
     */
    public function actionGetUserInfo()
    {
        Yii::$app->response->format = 'json';
        //验证求求方法（必须是post请求）
        if (!Yii::$app->request->isPost) {
            return ['code' => -100, 'message' => '非法的请求方式'];
        }

        //接收请求数据
        $post = Yii::$app->request->post();

        try {
            $sign = $post['sign'];
            //签名秘钥
            $signKey = Yii::$app->params['zrx']['signKey'];
            $res = Asset::sign($post, $signKey);
            if ($res == $sign) {    //验签成功
                //判断融资用户是否已经开户
                $user = User::findOne([
                    'safeIdCard' => SecurityUtils::encrypt(trim($post['idNo'])),
                    'type' => User::USER_TYPE_ORG,
                    'status' => User::STATUS_ACTIVE,
                    'is_soft_deleted' => 0,
                ]);
                if (!is_null($user)) {
                    $epayUser = EpayUser::findOne(['appUserId' => $user->id]);
                    if (!is_null($epayUser)) {

                        return ['code' => 0, 'data' => ['fundsAccountName' => $user->usercode, 'fundsAccountID' => $epayUser->epayUserId]];
                    }
                }

                return ['code' => -100, 'message' => '未开户'];
            } else {

                return ['code' => -100, 'message' => '验签失败'];
            }
        } catch (\Exception $e) {
            Yii::info('获取开户用户名等信息失败：' . $e->getMessage());

            return ['code' => -100, 'message' => '接口程序内部错误'];
        }
    }
}
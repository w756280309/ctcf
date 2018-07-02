<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-5-26
 * Time: 下午1:36
 */
namespace api\modules\asset\controllers;

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
                //未开户进行开户操作
                if (is_null($user)) {
                    try {
                        $extendInfo = json_decode($post['extendInfo'], true);
                        User::createOrgUser($extendInfo['phone'], $post['loanUserIdcard'], $post['name']);
                    } catch (\Exception $ex) {

                        return ['code' => 300, 'message' => '开户失败,' . $ex->getMessage()];
                    }
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
}
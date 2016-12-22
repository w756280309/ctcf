<?php

namespace api\modules\v1\controllers\notify;

use common\models\mall\PointOrder;
use common\models\mall\PointRecord;
use common\models\mall\ThirdPartyConnect;
use common\models\user\User;
use common\utils\TxUtils;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class MallController extends Controller
{
    //兑吧新建订单之后请求平台，平台进行积分扣除操作
    public function actionInit()
    {
        $requestUrl = Yii::$app->request->absoluteUrl;
        $requestParams = Yii::$app->request->queryParams;
        $appKey = Yii::$app->params['mall_settings']['app_key'];
        $appSecret = Yii::$app->params['mall_settings']['app_secret'];
        $result = ThirdPartyConnect::parseCreditConsume($appKey, $appSecret, $requestParams);
        $points = intval($result['credits']);
        $orderNum = $result['orderNum'];
        $publicId = $requestParams['uid'];
        $type = strtolower($requestParams['type']);
        if (
            empty($points)
            || empty($result['timestamp'])
            || empty($orderNum)
            || empty($publicId)
            || empty($type)
        ) {
            throw new \Exception('参数错误');
        }
        $orderTime = date('Y-m-d H:i:s', $result['timestamp'] / 1000);
        $thirdPartyConnect = ThirdPartyConnect::findOne(['publicId' => $publicId]);
        if (empty($thirdPartyConnect)) {
            throw new \Exception('不是温都会员');
        }
        $userId = $thirdPartyConnect->user_id;
        $user = User::findOne($userId);
        if (empty($user)) {
            throw new \Exception('不是温都会员');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $translation = Yii::$app->db->beginTransaction();
        try {
            $order = PointOrder::findOne(['orderNum' => $orderNum]);
            if (!empty($order)) {
                throw new \Exception('请勿重复下单');
            }
            if ($user->points < $points) {
                throw new \Exception('积分不足');
            }
            $res = Yii::$app->db->createCommand("UPDATE `user` SET `points` = `points` - :points WHERE `id` = :userId", ['points' => $points, 'userId' => $userId])->execute();
            if (!$res) {
                throw new \Exception('系统繁忙');
            }
            $user->refresh();
            $finalPoints = $user->points;
            $order = new PointOrder([
                'sn' => TxUtils::generateSn('PO'),
                'type' => $type,
                'orderNum' => $orderNum,
                'user_id' => $user->id,
                'points' => $points,
                'isPaid' => true,
                'orderTime' => $orderTime,
                'status' => PointOrder::STATUS_INIT,
                'mallUrl' => $requestUrl,
            ]);
            $res = $order->save();
            if (!$res) {
                throw new \Exception('系统繁忙');
            }
            $record = new PointRecord([
                'sn' => TxUtils::generateSn('PR'),
                'user_id' => $order->user_id,
                'ref_type' => PointRecord::TYPE_POINT_ORDER,
                'ref_id' => $order->id,
                'decr_points' => $order->points,
                'final_points' => $finalPoints,
                'recordTime' => date('Y-m-d H:i:s'),
            ]);
            $res = $record->save();
            if (!$res) {
                throw new \Exception('系统繁忙');
            }
            $translation->commit();
            return [
                'status' => 'ok',
                'errorMessage' => '',
                'bizId' => $order->sn,
                'credits' => $finalPoints,
            ];
        } catch (\Exception $ex) {
            $translation->rollBack();
            return [
                'status' => 'fail',
                'errorMessage' => $ex->getMessage(),
                'credits' => $user->points,
            ];
        }
    }

    //兑吧订单结果通知接口
    public function actionResult()
    {
        $requestParams = Yii::$app->request->queryParams;
        $appKey = Yii::$app->params['mall_settings']['app_key'];
        $appSecret = Yii::$app->params['mall_settings']['app_secret'];
        $result = ThirdPartyConnect::parseCreditNotify($appKey, $appSecret, $requestParams);
        $orderId = $result['bizId'];
        $res = $result['success'];
        $order = PointOrder::findOne(['sn' => $orderId]);
        if (empty($order)) {
            throw new \Exception('参数错误');
        }
        $user = $order->user;
        if (empty($user)) {
            throw new \Exception('参数错误');
        }
        if ($order->status !== PointOrder::STATUS_INIT) {
            exit('ok');//订单已经处理过
        }
        $translation = Yii::$app->db->beginTransaction();
        try {
            if ($res) {
                $order->status = PointOrder::STATUS_SUCCESS;
                $res = $order->save();
                if (!$res) {
                    throw new \Exception('系统繁忙');
                }
            } else {
                $res = Yii::$app->db->createCommand("UPDATE `user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $order->points, 'userId' => $order->user_id])->execute();
                if (!$res) {
                    throw new \Exception('系统繁忙');
                }
                $user->refresh();
                $finalPoints = $user->points;
                $record = new PointRecord([
                    'sn' => TxUtils::generateSn('PR'),
                    'user_id' => $order->user_id,
                    'ref_type' => PointRecord::TYPE_POINT_ORDER_FAIL,
                    'ref_id' => $order->id,
                    'incr_points' => $order->points,
                    'final_points' => $finalPoints,
                    'recordTime' => date('Y-m-d H:i:s'),
                ]);
                $res = $record->save();
                if (!$res) {
                    throw new \Exception('系统繁忙');
                }
                $order->status = PointOrder::STATUS_FAIL;
                $res = $order->save();
                if (!$res) {
                    throw new \Exception('系统繁忙');
                }
            }
            $translation->commit();
            exit('ok');
        } catch (\Exception $ex) {
            $translation->rollBack();
            exit('fail');
        }
    }
}
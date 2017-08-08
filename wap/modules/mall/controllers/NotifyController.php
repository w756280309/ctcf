<?php

namespace wap\modules\mall\controllers;

use common\models\code\GoodsType;
use common\models\mall\PointOrder;
use common\models\mall\PointRecord;
use common\models\mall\ThirdPartyConnect;
use common\models\user\User;
use common\utils\TxUtils;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class NotifyController extends Controller
{
    //兑吧新建订单之后请求平台，平台进行积分扣除操作
    public function actionInit()
    {
        $requestUrl = Yii::$app->request->absoluteUrl;
        Yii::info('[mall_notify][init_point_order] 兑吧发起扣除积分回调，回调url：' . $requestUrl, 'notify');
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
            Yii::info('[mall_notify][init_point_order] 兑吧发起扣除积分回调，回调失败：' . $ex->getMessage(), 'notify');
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
        $requestUrl = Yii::$app->request->absoluteUrl;
        Yii::info('[mall_notify][pint_order_result] 兑吧发起订单结果通知回调，回调url：' . $requestUrl, 'notify');
        $requestParams = Yii::$app->request->queryParams;
        $appKey = Yii::$app->params['mall_settings']['app_key'];
        $appSecret = Yii::$app->params['mall_settings']['app_secret'];
        $result = ThirdPartyConnect::parseCreditNotify($appKey, $appSecret, $requestParams);
        $res = $result['success'];
        $orderNum = $result['orderNum'];
        $order = PointOrder::findOne(['orderNum' => $orderNum]);
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
            if ($res === 'true') {
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
            Yii::info('[mall_notify][pint_order_result] 兑吧发起扣除积分回调，回调失败：' . $ex->getMessage(), 'notify');
            $translation->rollBack();
            exit('fail');
        }
    }

    //兑吧发起虚拟商品充值请求
    public function actionRecharge()
    {
        $requestUrl = Yii::$app->request->absoluteUrl;
        Yii::info('[mall_notify][voucher_recharge] 兑吧请求虚拟商品充值接口，回调url：' . $requestUrl, 'notify');
        $requestParams = Yii::$app->request->queryParams;
        $appKey = Yii::$app->params['mall_settings']['app_key'];
        $appSecret = Yii::$app->params['mall_settings']['app_secret'];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $translation = Yii::$app->db->beginTransaction();
        try {
            $result = ThirdPartyConnect::parseCreditConsume($appKey, $appSecret, $requestParams);
            $orderNum = $result['orderNum'];
            $publicId = $requestParams['uid'];
            $goodsTypeSn = $result['params'];
            $developBizId = $result['developBizId'];
            if (
                empty($orderNum)
                || empty($publicId)
                || empty($goodsTypeSn)
            ) {
                throw new \Exception('参数错误');
            }
            $thirdPartyConnect = ThirdPartyConnect::findOne(['publicId' => $publicId]);
            if (empty($thirdPartyConnect)) {
                throw new \Exception('不是温都会员');
            }
            $userId = $thirdPartyConnect->user_id;
            $user = User::findOne($userId);
            if (empty($user)) {
                throw new \Exception('不是温都会员');
            }

            $refData = null;
            if (!empty($developBizId)) {
                $order = PointOrder::findOne(['sn' => $developBizId]);
                if (!is_null($order)) {
                    $refData = ['type' => GoodsType::REF_TYPE_MALL_ORDER, 'id' => $order->id];
                }
            }

            //插入Voucher
            $voucher = GoodsType::issueVoucher($goodsTypeSn, $user, $refData, $orderNum);
            $voucher->save(false);

            $translation->commit();
            return [
                'status' => 'success',
                'credits' => $user->points,
                'supplierBizId' => $voucher->id,
            ];
        } catch (\Exception $ex) {
            Yii::info('[mall_notify][voucher_recharge] 兑吧发起虚拟商品充值回调，回调失败：' . $ex->getMessage(), 'notify');
            $translation->rollBack();
            return [
                'status' => 'fail',
                'errorMessage' => $ex->getMessage(),
                'supplierBizId' => '',
            ];
        }
    }

    //兑吧发起增加积分请求
    public function actionIncrease()
    {
        exit(1);
        $requestUrl = Yii::$app->request->absoluteUrl;
        Yii::info('[mall_notify][increase_point_order] 兑吧发起增加积分请求，请求url：' . $requestUrl, 'notify');
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
            $res = Yii::$app->db->createCommand("UPDATE `user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $userId])->execute();
            if (!$res) {
                throw new \Exception('系统繁忙');
            }
            $order = new PointOrder([
                'sn' => TxUtils::generateSn('PO'),
                'type' => $type,
                'orderNum' => $orderNum,
                'user_id' => $user->id,
                'points' => $points,
                'isPaid' => true,
                'orderTime' => $orderTime,
                'status' => PointOrder::STATUS_SUCCESS,
            ]);
            $res = $order->save();
            if (!$res) {
                throw new \Exception('系统繁忙');
            }
            switch ($type) {
                case 'game':
                    $remark = '积分游戏获得';break;
                case 'report':
                    $remark = '签到获得';break;
                case 'sign':
                    $remark = '签到获得';break;
                default:
                    $remark = '积分商城获得';
            }
            $user->refresh();
            $finalPoints = $user->points;
            $record = new PointRecord([
                'sn' => TxUtils::generateSn('PR'),
                'user_id' => $order->user_id,
                'ref_type' => PointRecord::TYPE_MALL_INCREASE,
                'ref_id' => $order->id,
                'incr_points' => $order->points,
                'final_points' => $finalPoints,
                'recordTime' => date('Y-m-d H:i:s'),
                'remark' => $remark,
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
}
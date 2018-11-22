<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\utils\TxUtils;

/**
 * 日志模型类.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class TradeLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tradelog';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    public function behaviors()
    {
        return [
             TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'txType' => 'Tx Type',
            'direction' => 'Direction',
            'txSn' => 'Tx Sn',
            'uid' => 'Uid',
            'requestData' => 'Request Data',
            'rawRequest' => 'Raw Request',
            'responseCode' => 'Response Code',
            'rawResponse' => 'Raw Response',
            'responseMessage' => 'Response Message',
            'duration' => 'Duration',
            'txDate' => 'Tx Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param array             $rqData 不包含签名的内容，可以为null
     * @param array             $rq     签名后的数据，可以为null
     * @param ResponseInterface $rp
     * @param $duration 记录同步请求响应时间
     * return tradelog 对象
     */
    public static function initLog($direction = 1, $rqData = null, $rq = null, $rp = null, $duration = 0)
    {
        $uid = 0;
        if (array_key_exists('user_id', $rqData)) {//联动用户标识
            $epayUser = epay\EpayUser::findOne(['epayUserId' => $rqData['user_id']]);
            $uid = !empty($epayUser) ? $epayUser->appUserId : '';
        } else if (array_key_exists('mer_cust_id', $rqData)) {//温都金服用户标识
            $uid = $rqData['mer_cust_id'];
        }

        $log = new self();
        $log->txType = array_key_exists('service', $rqData) ? $rqData['service'] : '';
        $log->direction = $direction;
        $log->txSn = array_key_exists('order_id', $rqData) ? $rqData['order_id'] : TxUtils::generateSn('L');
        $log->uid = $uid;
        $log->txDate = array_key_exists('mer_date', $rqData) ? $rqData['mer_date'] : date('Y-m-d H:i:s');
        $log->requestData = json_encode($rqData);//存储没有进行签名的数据
        $log->rawRequest = $rq;
        $log->duration = $duration;
        $log->responseMessage = '';
        if (null !== $rp) {
            $log->responseCode = $rp->get('ret_code');
            $log->rawResponse = json_encode($rp->toArray());
            $log->responseMessage = $rp->get('ret_msg');
        }

        return $log;
    }
}

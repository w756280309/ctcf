<?php
/**
 * 定时任务文件.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51.
 */
namespace console\controllers;

use yii\console\Controller;
use common\models\sms\SmsMessage;

class SmscrontabController extends Controller
{
    /**
     * 短信发送任务[文件锁].
     */
    public function actionSend()
    {
        $messages = SmsMessage::find()->where(['status' => SmsMessage::STATUS_WAIT])->orderBy('id desc')->all();
        foreach ($messages as $msg) {
            $result = \Yii::$container->get('sms')->send($msg);
            if ($result) {
                $msg->status = SmsMessage::STATUS_SENT;
            } else {
                $msg->status = SmsMessage::STATUS_FAIL;
            }
            $msg->save(false);
        }
    }
}

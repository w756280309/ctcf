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
        $runtimePath = \Yii::getAlias('@runtime');
        $lock_file = $runtimePath.'/sms.lock';
        $handle = fopen($lock_file,"w+");
        if (!file_exists($lock_file)) {
            fwrite($handle,'');
        }
        
        if($handle!==false){ //打开成功
            flock($handle, LOCK_EX);
            $limit = 100;//限制每次运行发送的短信数量
            $messages = SmsMessage::find()->where(['status' => SmsMessage::STATUS_WAIT])->limit($limit)->orderBy('id desc')->all();
            foreach ($messages as $msg) {
                $result = \Yii::$container->get('sms')->send($msg);
                if ($result) {
                    $msg->status = SmsMessage::STATUS_SENT;
                } else {
                    $msg->status = SmsMessage::STATUS_FAIL;
                }
                $ures = $msg->save(false);
                $msg_str = 'ID:' . $msg->id . "; 手机号:" . $msg->mobile . "; message:" . $msg->message . '; 响应码:' . $result . '; 操作结果:' . $ures;
                \Yii::trace($msg_str, 'sms');//消息格式Timestamp [IP address][User ID][Session ID][Severity Level][Category] Message Text
            }
            flock($handle,LOCK_UN);
            fclose($handle);  
        }
    }
}

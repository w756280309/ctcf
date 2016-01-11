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
                $msg->save(false);
            }
            flock($handle,LOCK_UN);
            fclose($handle);  
        }
    }
}

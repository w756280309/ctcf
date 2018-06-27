<?php

namespace common\log;

use Yii;
use yii\helpers\VarDumper;
use yii\log\FileTarget;
use yii\log\Logger;

class JsonTarget extends FileTarget
{
    public function formatMessage($message)
    {
        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);

        if (!is_string($text)) {
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }

        // 不支持设置prefix
        if (null !== $this->prefix) {
            throw new \Exception('Not implemented.');
        }

        $data = [
            'time' => $this->getTime($timestamp),
            'level' => $level,
            'category' => $category,
            'text' => $text,
        ];

        // 如果有Yii::$app实例，尝试获取IP、用户ID和会话ID
        if (null !== Yii::$app) {
            $request = Yii::$app->getRequest();
            $data['ip'] = $request instanceof Request ? $request->getUserIP() : null;

            /* @var $user \yii\web\User */
            $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
            if ($user && ($identity = $user->getIdentity(false))) {
                $userID = $identity->getId();
            } else {
                $userID = null;
            }
            $data['userId'] = $userID;

            /* @var $session \yii\web\Session */
            $session = Yii::$app->has('session', true) ? Yii::$app->get('session') : null;
            $data['sessionId'] = $session && $session->getIsActive() ? $session->getId() : null;
        }

        // TODO 什么时候会走这里？
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        if (!empty($traces)) {
            $data['traces'] = $traces;
        }

        return json_encode($data);
    }
}

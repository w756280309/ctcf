<?php

namespace common\controllers;

use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;

trait HelpersTrait
{
    public function render($view, $params = [])
    {
        if (Yii::$app->params['enable_dev_helpers']) {
            if (strncmp($view, '@', 1) === 0) {
                $file = Yii::getAlias($view);
            } elseif (strncmp($view, '//', 2) === 0) {
                $file = Yii::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            } elseif (strncmp($view, '/', 1) === 0) {
                $file = $this->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            } else {
                $file = $this->getViewPath() . DIRECTORY_SEPARATOR . $view;
            }

            Yii::$app->getResponse()->getHeaders()->set('DEV-VIEW-PATH', $file);
        }

        return parent::render($view, $params);
    }

    public function getAuthedUser()
    {
        return Yii::$app->user->getIdentity();
    }

    /**
     * 创建一个404异常对象
     *
     * @param string|null $message 异常消息
     * @param \Exception $previous 前置异常
     *
     * @return NotFoundHttpException
     */
    public function ex404($message = null, \Exception $previous = null)
    {
        return new NotFoundHttpException($message, 0, $previous);
    }

    /**
     * 根据ActiveRecord类名和查询条件，查找对象，如果不存在，抛出404异常
     *
     * @param string $class ActiveRecord类名
     * @param mixed $cond 查询条件
     *
     * @return object
     *
     * @throws NotFoundHttpException
     */
    public function findOr404($class, $cond)
    {
        $result = $class::findOne($cond);
        if (null === $result) {
            throw $this->ex404();
        }

        return $result;
    }

    /**
     * 判断请求来源是否来自微信
     * 忽略用户主动模拟微信请求及微信历史版本影响
     * @return bool
     */
    public function fromWx()
    {
        return $_SERVER["HTTP_USER_AGENT"] && false !== strpos($_SERVER["HTTP_USER_AGENT"], 'MicroMessenger');
    }

    /**
     * 返回APP版本号.
     */
    public function getAppVersion()
    {
        $version = array_filter(explode('/', $_SERVER['HTTP_USER_AGENT']));
        $versionCode = array_pop($version);

        return floatval($versionCode);
    }

    /**
     * 根据字符串或者Model对象返回错误信息
     * @param null|string|Model $modelOrMessage
     * @return array
     */
    public function createErrorResponse($modelOrMessage = null)
    {
        Yii::$app->response->statusCode = 400;
        $message = null;

        if (is_string($modelOrMessage)) {
            $message = $modelOrMessage;
        } elseif (
            $modelOrMessage instanceof Model
            && $modelOrMessage->hasErrors()
        ) {
            $message = current($modelOrMessage->getFirstErrors());
        }

        return ['message' => $message];
    }
}

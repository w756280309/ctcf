<?php

namespace common\controllers;

use Yii;

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
                $file = $this->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');;
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
}

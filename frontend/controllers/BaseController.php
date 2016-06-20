<?php
namespace frontend\controllers;

use common\controllers\HelpersTrait;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class BaseController extends Controller
{
    use HelpersTrait;

    protected $user;

    public function init()
    {
        error_reporting(E_ALL ^ E_NOTICE);

        if (!\Yii::$app->user->isGuest) {
            $this->user = \Yii::$app->user->getIdentity();
        }

        parent::init();
    }

    //记录进入开户、免密、绑卡流程的入口
    public function saveReferrer()
    {
        \Yii::$app->session->set('tx_url', \Yii::$app->request->referrer);
    }

    //返回来源页面或者返回到指定页面
    public function goReferrer($url = null)
    {
        if (\Yii::$app->session->has('tx_url')) {
            $url = \Yii::$app->session->get('tx_url');
            \Yii::$app->session->remove('tx_url');
        }
        if ($url) {
            return $this->redirect($url);
        } else {
            throw new NotFoundHttpException();
        }
    }
}

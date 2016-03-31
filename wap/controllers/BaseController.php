<?php
/**
 * Created by PhpStorm.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51.
 */
namespace app\controllers;

use common\controllers\HelpersTrait;
use yii\filters\AccessControl;
use yii\web\Controller;

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

    public function behaviors()
    {
        return [
            \common\filters\UserAccountAcesssControl::className(),//至于access之前是为了如果是app端登录先要拿token使用户登录
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'], //访客注册登录
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'], //登录用户退出
                    ],
                ],
            ],
            'requestbehavior' => [
                'class' => 'common\components\RequestBehavior',
            ],
            
        ];
    }
}

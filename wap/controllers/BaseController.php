<?php
/**
 * Created by PhpStorm.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51
 */
namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\user\User;

class BaseController extends Controller
{
    protected $uid;
    protected $user;
    protected $ubank;//用户绑卡信息
    protected $isDenyVisit = false; //是否拒绝访问 true 拒绝 false为允许访问
    public function init() {
        error_reporting(E_ALL ^ E_NOTICE);
        if(\Yii::$app->user->isGuest){
            $this->uid = 0;
        }else{
            $this->uid=\Yii::$app->user->id;
            $this->user= \Yii::$app->user->getIdentity();
            $this->ubank = $this->user->bank;
            $this->isDenyVisit = ($this->user->status == User::STATUS_DELETED) ? true : false;
        }
        parent::init();
    }
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],//访客注册登录
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],//登录用户退出
                    ],
                ],
            ],
            'requestbehavior' => [
                'class' => 'common\components\RequestBehavior'
            ],
            \common\filters\UserAccountAcesssControl::className()
        ];
    }
}
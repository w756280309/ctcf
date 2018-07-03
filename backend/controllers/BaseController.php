<?php

namespace backend\controllers;

use common\controllers\HelpersTrait;
use common\filters\AdminAcesssControl;
use common\models\user\User;
use yii\web\Controller;
use yii\filters\AccessControl;

class BaseController extends Controller
{
    public $layout = '@app/views/layouts/frame';
    public $alert = 0; //是否提示
    protected $toUrl = ''; //是否成功后跳转
    protected $msg = '操作成功'; //提示内容
    protected $time = 1; //提示时间
    protected $admin_id = 0;
    protected $admin_name = '';

    use HelpersTrait;

    public function init()
    {
        error_reporting(E_ALL ^ E_NOTICE);

        $user = $this->getAuthedUser();
        $this->admin_id = $user->id;
        $this->admin_name = $user->username;

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
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            AdminAcesssControl::className(),
        ];
    }

    public function render($view, $params = array())
    {
        if ($this->alert) {
            $script = '(function(wcgtip, $, msg,tourl,time){
                        layer.msg(msg,{time:time*1000,icon:'.$this->alert.",shade:[0.8, 'gray']}, function(){
                            if(tourl!=''){
                                location.href=tourl
                            }
                        });
                        }(window.wcgtip = window.wcgtip || {}, jQuery ,'".$this->msg."','".$this->toUrl."','".$this->time."'));";
            \Yii::$app->view->registerJs($script);
        }

        return parent::render($view, $params);
    }
    /**
     * 获取未被软删除的全部融资方信息.
     */
    public function orgUserInfo(array $type)
    {
        $u = User::tableName();
        $b = \common\models\user\Borrower::tableName();
        return  User::find()
            ->innerJoinWith('borrowerInfo')
            ->where(["$u.type" => User::USER_TYPE_ORG])
            ->andWhere(['is_soft_deleted' => 0])
            ->andWhere(['in', "$b.type", $type])
            ->orderBy(['sort' => SORT_DESC])
            ->select("org_name")
            ->indexBy("userId")
            ->column();
    }
}

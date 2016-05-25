<?php
/**
 * Created by PhpStorm.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51.
 */
namespace backend\controllers;

use common\controllers\HelpersTrait;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

class BaseController extends Controller
{
    public $layout = 'frame';
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
        $this->admin_id = Yii::$app->user->id;
        $this->admin_name = Yii::$app->user->identity->username;
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
            \common\filters\AdminAcesssControl::className(),
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
}

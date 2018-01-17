<?php

namespace common\view;

use Yii;
use yii\web\View;

class UdeskWebIMHelper
{
    public static function init($view)
    {
        $usercode = null;
        $realName = '非会员或未登录';
        $authedUser = Yii::$app->user->identity;
        $session_key = '1234567891011159';
        if (null !== $authedUser) {
            $usercode = $authedUser->usercode;
            $realName = $authedUser->real_name;
            $session_key = $usercode;
        }
        $randomNumber = bin2hex(random_bytes(8));
        $nonce = $randomNumber;
        $timestamp = time().'000';
        $im_user_key = Yii::$app->params['u_desk']['im_user_key'];
        $web_token = $usercode;
        $sign_str = "nonce=".$nonce."&timestamp=".$timestamp."&web_token=".$web_token."&".$im_user_key;
        $sign_str = sha1($sign_str);
        $sign_str = strtoupper($sign_str);
        $udeskCode = Yii::$app->params['u_desk']['code'];
        $udeskLink = Yii::$app->params['u_desk']['link'];
        $_js = <<<JS
        (function(a,h,c,b,f,g){a["UdeskApiObject"]=f;a[f]=a[f]||function(){(a[f].d=a[f].d||[]).push(arguments)};g=h.createElement(c);g.async=1;g.charset="utf-8";g.src=b;c=h.getElementsByTagName(c)[0];c.parentNode.insertBefore(g,c)})(window,document,"script",'//assets-cli.udesk.cn/im_client/js/udeskApi.js',"ud");
           ud({
                "code": '$udeskCode',
                "link": '$udeskLink',
                "mode": 'inner',
                "color": "#FF0000",
                "pos_flag": "crb",
                "session_key": '$session_key',
                "language": "zh-cn",
                "onlineText": "联系客服，在线咨询",
                "offlineText": "客服下班，请留言",
                "targetSelector": "#btn_udesk_im",
                "manualInit": false,
                "selector": "",
                "panel": {                 //会话面板配置参数
                    "css": {
                        "top": "0",
                        "left": "0",
                        "bottom": "0",
                        "right": "0"
                    }
                },
                "customer":{
                "nonce": '$nonce',
                "signature": '$sign_str',
                'timestamp': '$timestamp',
                "web_token": '$web_token',
                "c_name": '$realName($usercode)',
                }
            });
JS;
        $_css = <<<CSS
             #btn_udesk_im{
                width: 100%;
                text-align: center;
                margin:0 auto;
                font-size: 14px;
                color: #000;
                cursor: pointer;
            }
            #btn_udesk_im img{
                width: 20px;
                height:20px;
                margin-right: 5px;
                margin-bottom: 3px;
            }
CSS;

        $view->registerJs($_js, View::POS_HEAD, 'body_close_udesk');
        $view->registerCss($_css);
    }
}

<?php

namespace common\view;

use yii\web\View;

class BaiduTongjiHelper
{
    const WAP_KEY = 'd2417f8d221ffd4b883d5e257e21736c';
    const PC_KEY = 'd2417f8d221ffd4b883d5e257e21736c'; // 合并PC站统计
    //const PC_KEY = '22888bd72b3ee39b96705e3ce4492484';

    public static function registerTo($viewObj, $key)
    {
        $_js = <<<JS
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?$key";
  var s = document.getElementsByTagName("script")[0];
  s.parentNode.insertBefore(hm, s);
})();
JS;

        if (\Yii::$app->params['enable_baidu_tongji']) {
            $viewObj->registerJs($_js, View::POS_HEAD, 'body_close');
        }
    }
}


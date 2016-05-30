<?php

namespace common\view;

use yii\web\View;

class AnalyticsHelper
{
    public static function registerTo($viewObj)
    {
        if (!\Yii::$app->params['enable_analytics']) {
            return;
        }

        $baiduKey = \Yii::$app->params['baidu_tongji_key'];
        $gaId = \Yii::$app->params['ga_tracking_id'];
        if (empty($baiduKey) || empty($gaId)) {
            return;
        }

        $_js = <<<JS
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?$baiduKey";
  var s = document.getElementsByTagName("script")[0];
  s.parentNode.insertBefore(hm, s);
})();

(function(i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r]=i[r] || function() {
        (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o), m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
})(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
ga('create', '$gaId', 'auto');
ga('send', 'pageview');
JS;

        $viewObj->registerJs($_js, View::POS_HEAD, 'body_close');
    }
}

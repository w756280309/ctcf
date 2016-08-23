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
        $ptweb_account = \Yii::$app->params['ptweb_account'];
        if (empty($baiduKey) || empty($gaId) || empty($ptweb_account)) {
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
ga('create', '$gaId', '.wenjf.com');
ga('send', 'pageview');

window._pt_lt = new Date().getTime();
window._pt_sp_2 = [];
_pt_sp_2.push('setAccount,$ptweb_account');
var _protocol = (("https:" == document.location.protocol) ? " https://" : " http://");
(function() {
var atag = document.createElement('script'); atag.type = 'text/javascript'; atag.async = true;
atag.src = _protocol + 'js.ptengine.cn/pta.js';
var stag = document.createElement('script'); stag.type = 'text/javascript'; stag.async = true;
stag.src = _protocol + 'js.ptengine.cn/pts.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(atag, s); s.parentNode.insertBefore(stag, s);
})();
JS;

        $viewObj->registerJs($_js, View::POS_HEAD, 'body_close');
    }
}

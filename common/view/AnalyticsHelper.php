<?php

namespace common\view;

use yii\web\View;

class AnalyticsHelper
{
    public static function registerTo($viewObj)
    {
        if (!\Yii::$app->params['analytics_enabled']) {
            return;
        }

        $pkId = '';
        if (defined('CLIENT_TYPE') && in_array(CLIENT_TYPE, ['wap', 'app', 'pc'])) {
            $pkId = \Yii::$app->params['analytics_pk_'.CLIENT_TYPE.'_id'];
        }
        $gaId = \Yii::$app->params['analytics_ga_id'];
        $gioId = \Yii::$app->params['analytics_gio_id'];

        $_js = <<<JS
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//d.wendujf.com/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '$pkId']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'d.js'; s.parentNode.insertBefore(g,s);
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

var _vds = _vds || [];
window._vds = _vds;
(function(){
    _vds.push(['setAccountId', '$gioId']);
    (function() {
        var vds = document.createElement('script');
        vds.type='text/javascript';
        vds.async = true;
        vds.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'dn-growing.qbox.me/vds.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(vds, s);
    })();
})();
JS;

        $viewObj->registerJs($_js, View::POS_HEAD, 'body_close');
    }
}

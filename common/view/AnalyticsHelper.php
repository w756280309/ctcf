<?php

namespace common\view;

use Yii;
use yii\web\View;

class AnalyticsHelper
{
    public static function registerTo($viewObj)
    {
        if (!\Yii::$app->params['analytics_enabled']) {
            return;
        }
//var_dump(Yii::$app->user->id);exit;
        $pkId = '';
        if (defined('CLIENT_TYPE') && in_array(CLIENT_TYPE, ['wap', 'app', 'pc'])) {
            $pkId = \Yii::$app->params['analytics_pk_'.CLIENT_TYPE.'_id'];
        }
        $gaId = \Yii::$app->params['analytics_ga_id'];
        $gioId = \Yii::$app->params['analytics_gio_id'];

        $_js = <<<JS
var _paq = _paq || [];
_paq.push(['setDomains', [
'mp.weixinbridge.com',
'open.weixin.qq.com',
'pay.soopay.net'
]]);
JS;

        $authedUserId = Yii::$app->user->id;
        if (null !== $authedUserId) {
            $_js .= <<<JS
_paq.push(['setUserId', '$authedUserId']);
JS;

            $authedUser = Yii::$app->user->identity;
            if (null !== $authedUser) {
                $isLender = $authedUser->getUserIsInvested() ? 'yes': 'no';
                $_js .= <<<JS
_paq.push(['setCustomDimension', 1, '$isLender']);
JS;
            }
        }

        $_js .= <<<JS
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//d.wendujf.com/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '$pkId']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();

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

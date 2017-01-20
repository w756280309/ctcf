$(function () {
    //获取有标记渠道的cookie
    var source = Cookies.get('campaign_source');
    //获取页面的'hmsr', 'utm_source', 'trk_s'参数
    var sign = ['hmsr', 'utm_source', 'trk_s'];
    for (var i = 0; i < sign.length; i++) {
        var r = new RegExp("(^|&)" + sign[i] + "=([^&]*)(&|$)");
        var result = window.location.search.substr(1).match(r);
        if (result && result[2]) {
            source = result[2];
        }
    }
    //记录90天cookie
    if (source) {
        Cookies.set('campaign_source', source, {expires: 90, path: '/'});
    }
});
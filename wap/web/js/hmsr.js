$(function () {
    var r = new RegExp("(^|&)" + 'hmsr' + "=([^&]*)(&|$)");
    var result = window.location.search.substr(1).match(r);
    if (result && result[2]) {
        var res = $.cookie('campaign_source', result[2], {expires: 3});
    }
});
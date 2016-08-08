$(document).ajaxError(function(event, jqXHR) {
    var errMsg = jqXHR.status === 403
        ? '您没有权限进行此操作'
        : '未知错误，请刷新重试或联系技术人员';

    newalert(0, errMsg, 1);
    cloaseLoading();
});
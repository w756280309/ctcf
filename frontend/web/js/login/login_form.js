/** 本文件用于登录弹框的引入. */

//获取登录页面
function getLoginHtml()
{
    $.ajax({
        beforeSend: function (req) {
            req.setRequestHeader("Accept", "text/html");
        },
        'url': '/site/login-form',
        'type': 'get',
        'dataType': 'html',
        'success': function (html) {
            $('body').append(html);
            $('.login-mark').fadeIn();
            $('.loginUp-box').fadeIn();
        }
    });

    return '';
}

//处理ajax登录
function login()
{
    document.documentElement.style.overflow = 'hidden';   //禁用页面上下滚动效果

    //如果已经加载过登录页面，则直接显示
    if ($('.login-mark').length > 0) {
        $('.login-mark').fadeIn();
        $('.loginUp-box').fadeIn();
    } else {
        //加载登录页面
        getLoginHtml();
    }
}
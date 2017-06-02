/*
 微信分享 添加右上角图标提示图片
 */
function weiXinShareTips(Obj){
    //  微信和app分享
    var html =
         '<div class="mark-box" style="display: none;position: fixed;left: 0;bottom:0;width: 100%;height: 100%;background: #000;opacity: 0.6;z-index: 11;" ></div>'
        +'<div class="share-box" style="display: none;position: fixed;left: 0;bottom:0;width: 100%;height: 100%;z-index: 12;text-align: right;">'
            +'<img class="share-box-img" style="float: right;width: 80%;" src="/images/share.png" alt="">'
        +'</div>';

    //  判断域名为微信
    if(navigator.userAgent.indexOf('MicroMessenger') > -1 ) {
        Obj.on('click',function(){
            $('body').prepend(html);
            $('.mark-box').show();
            $('.share-box').show();
        });
        var e = event || e;
        $('body').on('singleTap',function(e){
            if($(e.target).hasClass('share-box') || $(e.target).hasClass('share-box-img')){
                $('.share-box').hide().remove();
                $('.mark-box').hide().remove();
            }
        });
    }
}
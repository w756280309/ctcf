/**
 * 居中弹窗.
 */
function toastCenter(val, active)
{
    var $alert = $('<div class="error-info" style="display: block; position: fixed;"><div>' + val + '</div></div>');
    $('body').append($alert);
    //js动态控制弹框高度
    $(function(){
        function autoHeight(H,errorH) {
            if(errorH< H){
                $(".error-info").height(H+"px");
                $(".error-info div").height(H+"px");
            } else {
                $(".error-info").height("auto");
                $(".error-info div").height(errorH);
            }
        };

        function parseUA() {
            var u = navigator.userAgent;
            return { //移动终端浏览器版本信息 
                android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或uc浏览器 
                iPhone: u.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器 
            };
        };

        var phone = parseUA();
        var errorH = $(".error-info").height();
        var dpr = window.devicePixelRatio;
        if(phone.iPhone) {
            if (dpr == 2){
                autoHeight(200,errorH);
            } else if(dpr == 3){
                autoHeight(300,errorH);
            } else {
                autoHeight(100,errorH);
            }
        } else {
            autoHeight(100,errorH);
        };
    });

    $alert.find('div').width($alert.width());
    setTimeout(function () {
        $alert.fadeOut();
        setTimeout(function () {
            $alert.remove();
        }, 200);
        if (active) {
            active();
        }
    }, 2000);
}

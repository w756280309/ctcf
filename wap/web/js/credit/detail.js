function torefer(val, callback)
{
    var $alert = $('<div class="error-info" style="display: block"><div>' + val + '</div></div>');
    $alert.insertAfter($('.toRefer'));
    $alert.find('div').width($alert.width());
    setTimeout(function () {
        $alert.fadeOut();
        setTimeout(function () {
            $alert.remove();
        }, 200);
        if (callback) {
            callback();
        }
    }, 2000);
}
function alertReferBox(val, trued) {
    var chongzhi = $('<div class="mask" style="display:block;"></div><div class="bing-info show"> <div class="bing-tishi">温馨提示</div> <p class="tishi-p" style="line-height: 20px;"> ' + val + '，即将跳转到相应的页面完成相应的操作</p> <div class="bind-btn"> <span class="true">我知道了</span> </div> </div>');
    $(chongzhi).insertAfter($('.toRefer'));
    $('.bing-info').on('click', function () {
        $(chongzhi).remove();
        if (typeof trued !== 'undefined') {
            trued();
        }
    });
}
$(function() {
    $('.m4 img').on('click',function() {
        $('#chart-box').stop(true,false).fadeToggle();
    });

    function getCountDown(timestamp)
    {
        function daojishi()
        {
            var nowTime = new Date();
            var endTime = new Date(timestamp * 1000);

            if (nowTime >= endTime) {
                d = hour = min = sec = 0;
            } else {
                var t = endTime.getTime()-nowTime.getTime();
                var d = Math.floor(t/1000/60/60/24);
                var hour = Math.floor(t/1000/60/60%24);
                var min = Math.floor(t/1000/60%60);
                var sec = Math.floor(t/1000%60);
            }

            var countDownTime ="距离结束："+d+"天"+hour+"时"+min+"分"+sec+"秒";
            $(".daojishi .col-xs-12 div").html(countDownTime);
        }
        daojishi();
        setInterval(function() {
            daojishi();
        }, 1000);
    }
    getCountDown(remainTime);

    $('#check-in').bind('click', function () {
        var note_id = $('.toRefer').attr('note-id');
        var xhr = $.get('/credit/note/check', function (data) {
            if (data.tourl != undefined && data.code === 1) {
                if (data.message === '请登录') {
                    torefer(data.message, function() {
                        location.href = data.tourl;
                    });
                } else {
                    alertReferBox(data.message,function(){
                        location.href = data.tourl;
                    });
                }
            } else {
                location.href = '/credit/order/order?note_id=' + note_id;
            }
        });
        xhr.fail(function () {
            torefer('系统繁忙，请稍后重试！');
        });
    });
});
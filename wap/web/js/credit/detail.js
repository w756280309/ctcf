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
function alertReferBox(val, isConfirm, callback)
{
    if (isConfirm) {
        var chongzhi = $('<div class="mask" style="display: block;"></div><div class="bing-info show" style="position: fixed;margin: 0px;left:15%"> <p class="tishi-p" style="line-height: 20px;">'+ val +'</p> <div class="bind-btn"> <span class="no" style="border-right: 1px solid #ccc;">取消</span> <span class="yes" style="border-left: 1px solid #ccc;">确定</span></div> </div>');
    } else {
        var chongzhi = $('<div class="mask" style="display:block;"></div><div class="bing-info show"> <div class="bing-tishi">温馨提示</div> <p class="tishi-p" style="line-height: 20px;"> ' + val + '，即将跳转到相应的页面完成相应的操作</p> <div class="bind-btn"> <span class="yes true">我知道了</span> </div> </div>');
    }
    $(chongzhi).insertAfter($('.toRefer'));
    $('.bind-btn .yes').on('click', function () {
        $(chongzhi).remove();
        if (typeof callback !== 'undefined') {
            callback();
        }
    });
    $('.bind-btn .no').on('click', function () {
        $(chongzhi).remove();
    });
}
var dealCancel = function cancelNote() {
    var noteBtn = $('#cancel-note');
    var note_id = $('.toRefer').attr('note-id');

    if (noteBtn.hasClass('twoClick')) {
        return false;
    }

    noteBtn.addClass('twoClick');
    var xhr = $.get('/credit/trade/cancel?id=' + note_id, function (data) {
        if (0 === data.code) {
            location.href = "/credit/trade/assets?type=2";
            return false;
        }
        torefer(data.message);
    });

    xhr.always(function () {
        noteBtn.removeClass('twoClick');
    });

    xhr.fail(function () {
        noteBtn.removeClass('twoClick');
        alert('系统繁忙，撤销失败，请稍后重试!');
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
                    alertReferBox(data.message, 0, function () {
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

    $('#cancel-note').bind('click', function () {
        alertReferBox('确认撤销当前转让中的项目？已转让的不能撤回，发起后立即生效。', 1, dealCancel);
    });
});
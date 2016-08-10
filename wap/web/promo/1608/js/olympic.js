$(function() {
    $(window).load(function(){
        //初始化
        var hL=$('.help-box ul li').eq(0).height();
        $('.help-box ul li').css({height:hL});
        var aH=$('.award-box').height()/2;
        $('.award-box').css({marginTop:-aH});
        var adH=$('.address-box').height()/2;
        $('.address-box').css({marginTop:-adH});
        var iH=$('.invite-box').height()/2;
        $('.invite-box').css({marginTop:-iH});
        var lH=$('.login-box').height()/2;
        $('.login-box').css({marginTop:-lH});
        var tH=$('.touzi-box').height()/2;
        $('.touzi-box').css({marginTop:-tH});
    });
    $('.close-box').on('click',function(){
        $('.award-box').fadeOut('fast');
        $('.mark-box').hide();
    });
    $('.close-address').on('click',function(){
        $('.address-box').fadeOut('fast');
        $('.mark-box').hide();
    });
    $('.close-invite').on('click',function(){
        $('.invite-box').fadeOut('fast');
        $('.mark-box').hide();

        if ($('.invite-inner').hasClass('success')) {
            location.reload();
        }
    });
    $('.close-login').on('click',function(){
        $('.login-box').fadeOut('fast');
        $('.mark-box').hide();
    });

    //点击助力显示对应奖品
    var award = [
        '../../promo/1608/images/olympic/pingpangqiupai.jpg',
        '../../promo/1608/images/olympic/yongjing.jpg',
        '../../promo/1608/images/olympic/shoutao.jpg',
        '../../promo/1608/images/olympic/paiqiu.jpg',
        '../../promo/1608/images/olympic/lanqiu.jpg',
        '../../promo/1608/images/olympic/shouhuan.jpg',
        '../../promo/1608/images/olympic/zhuqiu.jpg',
        '../../promo/1608/images/olympic/wangqiupai.jpg',
    ];

    $('.help-btn').on('click', function() {
        var index = $('.help-btn').index(this);
        $('.award-inner img').attr({src: award[index]});
        $('.award-box').fadeIn('fast');
        $('.mark-box').show();
        lingqu(index + 1);
    });

    function lingqu(a)
    {
        $('.award-inner div').on('click', function() {
            $('.award-box').fadeOut('fast');
            var status = parseInt($('#status').val());
            if (status === 1) {
                $('.login-box').fadeIn('fast');
                $('.mark-box').show();
            } else if (status === 2) {
                $('.invite-box').fadeIn('fast');
                $('.mark-box').show();
                $('.invite-inner span').html('本活动仅限新用户，邀请好友参与拿邀请奖励！');
                $('.invite-btn').html('<span onclick="location.href=\'/user/invite\'">邀请好友</span>');
            } else if (status === 3) {
                $('.invite-box').fadeIn('fast');
                $('.mark-box').show();
                $('.invite-inner span').html('单笔投资未满1万元哦！投资成功即可领取奖励！');
                $('.invite-btn').html('<span onclick="location.href=\'/deal/deal/index\'">马上投资</span>');
            } else if (status === 4) {
                $('#type').val(a);
                $('.address-box').fadeIn('fast');
                $('.mark-box').fadeIn('fast');
            } else if (status === 5) {
                $('.invite-box').fadeIn('fast');
                $('.mark-box').show();
                $('.invite-inner span').html('您已经领取过奖品了，去邀请好友吧！');
                $('.invite-btn').html('<span onclick="location.href=\'/user/invite\'">邀请好友</span>');
            } else if (status === 6) {
                $('.invite-box').fadeIn('fast');
                $('.mark-box').show();
                $('.invite-inner span').html('活动还未开始！');
                $('.invite-btn').html('确定');
            } else if (status === 7) {
                $('.invite-box').fadeIn('fast');
                $('.mark-box').show();
                $('.invite-inner span').html('活动已结束！');
                $('.invite-btn').html('确定');
            }
        })
    }
    //保存地址
    $('.save-address').on('click',function() {
        if ($('#address').val() === '') {
            alert('地址不能为空');
            return;
        }

        var $form = $('#form');
        var xhr = $.post(
            $form.attr('action'),
            $form.serialize()
        );

        xhr.done(function(data) {
            if (data.code) {
                $('.address-box').fadeOut('fast');
                $('.invite-box').fadeIn('fast');
                $('.mark-box').show();
                $('.invite-inner span').html('恭喜您！领取成功！<br>我们将在10个工作日内<br>发货！<br>您也可以邀请好友参加<br>活动！领取<br>邀请奖励！');
                $('.invite-btn').html('<span onclick="location.href=\'/user/invite\'">邀请好友</span>');
                $('.invite-inner').addClass('success');
            } else {
                alert('地址提交失败，请重试!');
            }
        });

        xhr.fail(function(jqXHR) {
            var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                ? jqXHR.responseJSON.message
                : '未知错误，请刷新重试或联系客服';

            alert(errMsg);
        });
    });

    $('.invite-btn span').on('click', function() {
        $('.invite-box').fadeOut('fast');
        $('.mark-box').hide();
    });

    $('.mark-box').on('click',function() {
        $('.award-box').fadeOut('fast');
        $('.mark-box').hide();
        $('.invite-box').fadeOut('fast');
        $('.address-box').fadeOut('fast');
        $('.login-box').fadeOut('fast');
        $('.touzi-box').fadeOut('fast');

        if ($('.invite-inner').hasClass('success')) {
            location.reload();
        }
    });

    $type = parseInt($('#type').val());
    if ($type) {
        $('.zhuli li div p').eq($type - 1).html('已领取');
    }
});
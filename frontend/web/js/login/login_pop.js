$(function() {
    var lH=$('.loginUp-box').height()/2;

    $('.loginUp-box').css({marginTop:-lH});

    $('.loginUp-btn').hover(function(){
        $('.loginUp-btn').css({background:'#f41c11'});
    },function(){
        $('.loginUp-btn').css({background:'#f44336'});
    });

    $('.close').on('click',function(){
        $('.login-mark').fadeOut();
        $('.loginUp-box').fadeOut();
        $('.phone_err').hide().html('');
        $('.pass_err').hide().html('');
        $('.verity_err').hide().html('');
        $('#phone').val('').removeClass('error-border');
        $('#password').val('').removeClass('error-border');
        $('#verity').val('').removeClass('error-border');

        document.documentElement.style.overflow = 'auto';
    })

    //键盘按下ESC时关闭窗口!
    $(document).keydown(function(e) {
        if (e.keyCode === 27) {
            $('.close').click();
        }
    });
});
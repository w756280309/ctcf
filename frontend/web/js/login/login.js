$(function() {
    var flags = true;
    $('.login-check').on('click', function() {
        if (!flags) {
            $("input[name='remember']").attr("checked", false);
            $('.agree').val('no');
            flags = true;
        } else if (flags) {
            $("input[name='remember']").attr("checked", true);
            $('.agree').val('yes');
            flags = false;
        }
    });
    $('.login-btn').hover(function() {
        $('.login-btn').css({background: '#f41c11'});
    }, function() {
        $('.login-btn').css({background: '#f44336'});
    });
});
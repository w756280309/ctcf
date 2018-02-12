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
});
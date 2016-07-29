$(document).ready(function () {
    FastClick.attach(document.body);
    $('.transform-box').height($('.transform-front-box').height());
    $('.content-picture').on('click', function () {
        $(this).parents('.fixed-box').hide().siblings('.fixed-float').hide();
    });
    $('#transform-icon').on('click', function () {
        $(this).addClass('transform-start');
        setTimeout(function () {
            $('.transform-front-box').fadeOut(200);
            $('.transform-back-box').fadeIn(200);
        }, 500);
    });
});
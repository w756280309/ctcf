$(function(){
    $('.tr-click:odd').find('td').addClass('td-back-color');
})
//查看下拉
$(document).ready(function () {
    $('.tr-click:odd').find('td').addClass('td-back-color');
    $('.tip-cursor').on('click', function () {
        if ($(this).find('.tip-icon-enna').hasClass('tip-icon-top')) {
            $(this).parents('.tr-click').next('.tr-show').show();
            $(this).find('.tip-icon-enna').removeClass('tip-icon-top').addClass('tip-icon-bottom');
        } else {
            $(this).parents('.tr-click').next('.tr-show').hide();
            $(this).find('.tip-icon-enna').removeClass('tip-icon-bottom').addClass('tip-icon-top');
        }
    });
});
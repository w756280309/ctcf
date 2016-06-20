/*有三个文件引入此js--收益中的项目--待成立的项目--已还清的项目--*/
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
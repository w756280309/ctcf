$(function () {
    var mask = $('.mask');
    var idcard_message = $('.idcard_message');
    $('#idcard_confirm').click(function () {
        mask.hide();
        idcard_message.hide();
    });
    $('.name-text').bind('blur', function () {
        validate_name()
    });
    $('.identity-text').bind('blur', function () {
        validate_idcard();
    });
});
function validate_idcard() {
    var identityisok = false;
    var id_prg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
    var identityText = $.trim($('.identity-text').val());
    if (identityText.indexOf(" ") >= 0) {
        $('.identity-text').siblings('.tip_pop').show().find('.identity-content').text('请不要输入空格');
        identityisok = false;
    } else if (identityText.length !== 18) {
        $('.identity-text').siblings('.tip_pop').show().find('.identity-content').text('身份证暂只支持18位');
        identityisok = false;
    } else if (!id_prg.test(identityText)) {
        $('.identity-text').siblings('.tip_pop').show().find('.identity-content').text('身份证不合法');
        identityisok = false;
    } else {
        $('.identity-text').siblings('.tip_pop').hide().find('.identity-content').text('');
        identityisok = true;
    }
    return identityisok;
}
function validate_name() {
    var nameisok = false;
    var name_prg = /^[\u4E00-\u9FA5]{1,6}$/;
    var nameText = $.trim($('.name-text').val());
    if (!nameText) {
        $('.name-text').siblings('.tip_pop').show().find('.name-content').text('请输入真实姓名');
        nameisok = false;
    } else if (nameText.indexOf(" ") >= 0) {
        $('.name-text').siblings('.tip_pop').show().find('.name-content').text('请不要输入空格');
        nameisok = false;
    } else if (!name_prg.test(nameText)) {
        $('.name-text').siblings('.tip_pop').show().find('.name-content').text('姓名不合法');
        nameisok = false;
    } else {
        $('.name-text').siblings('.tip_pop').hide().find('.name-content').text('');
        nameisok = true;
    }
    return nameisok;
}
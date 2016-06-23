$(function () {
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
        $('.identity .err-info').text('身份证号中不能有空格');
        $('.identity .err-info').show();

        identityisok = false;
    } else if (identityText.length !== 18) {
        $('.identity .err-info').text('身份证号暂只支持18位');
        $('.identity .err-info').show();

        identityisok = false;
    } else if (!id_prg.test(identityText)) {
        $('.identity .err-info').text('身份证号不合法');
        $('.identity .err-info').show();

        identityisok = false;
    } else {
        $('.identity-text').css('border','1px solid #e4e4e8');
        $('.identity .err-info').text('');
        $('.identity .err-info').hide();

        identityisok = true;
    }

    if (!identityisok) {
        $('.identity-text').css('border','1px solid #f44336');
    }

    return identityisok;
}
function validate_name() {
    var nameisok = false;
    var name_prg = /^[\u4E00-\u9FA5]{1,6}$/;
    var nameText = $.trim($('.name-text').val());
    if (!nameText) {
        $('.name .err-info').text('真实姓名不能为空');
        $('.name .err-info').show();
        nameisok = false;
    } else if (nameText.indexOf(" ") >= 0) {
        $('.name .err-info').text('真实姓名中不能有空格');
        $('.name .err-info').show();
        nameisok = false;
    } else if (!name_prg.test(nameText)) {
        $('.name .err-info').text('真实姓名不合法');
        $('.name .err-info').show();
        nameisok = false;
    } else {
        $('.name-text').css('border','1px solid #e4e4e8');
        $('.name .err-info').text('');
        $('.name .err-info').hide();
        nameisok = true;
    }

    if (!nameisok) {
        $('.name-text').css('border','1px solid #f44336');
    }

    return nameisok;
}
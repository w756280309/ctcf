$(document).ready(function () {
    $('.confirm-inner').on('click', function () {
        $('.mask,.mask-over').hide();
    });
    function showInputBorder(obj, status) {
        if (!status) {
            obj.css('border', '1px solid #f44336');
        } else {
            obj.css('border', '1px solid #e4e4e8');
        }
    }

    var nameisok = false;
    var phoneisok = false;
    var moneyisok = false;
    $('.name-text').on('blur', function () {
        var nameText = $.trim($('.name-text').val());
        if (!nameText) {
            $("#name_err").html('姓名不能为空');
            nameisok = false;
        } else if (nameText.indexOf(" ") >= 0) {
            $("#name_err").html('请不要输入空格');
            nameisok = false;
        } else {
            $("#name_err").html('');
            nameisok = true;
        }
        showInputBorder($(this), nameisok);
    });
    $('.phone-text').on('blur', function () {
        var phonereg = /^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|17[0-9]{9}|18[0-9]{9}$/g;
        var phoneText = $.trim($(".phone-text").val());
        if (!phoneText) {
            $("#phone_err").html('手机号不能为空');
            phoneisok = false;
        } else if (!phonereg.test(phoneText)) {
            $("#phone_err").html('手机号格式错误');
            phoneisok = false;
        } else {
            $("#phone_err").html('');
            phoneisok = true;
        }
        showInputBorder($(this), phoneisok);
    });
    $('.money-text').on('blur', function () {
        var moneyText = $.trim($('.money-text').val());
        var reg = new RegExp("^[0-9]*[1-9][0-9]*$");
        if (!moneyText) {
            $("#money_err").html('预约金额不能为空');
            moneyisok = false;
        } else if (moneyText.indexOf(" ") >= 0) {
            $("#money_err").html('请不要输入空格');
            moneyisok = false;
        } else if (!reg.test(moneyText)) {
            $("#money_err").html('请输入正确的预约金额');
            moneyisok = false;
        } else {
            $('.money-text').parents('.combine-single').find('.tip_pop').hide().find('.tip_pop-content').text('');
            $("#money_err").html('');
            moneyisok = true;
        }
        showInputBorder($(this), moneyisok);
    });
    /*点击提交*/
    $('.confirm-arrange').on('click', function (e) {
        e.preventDefault();
        $('.name-text').trigger('blur');
        $('.phone-text').trigger('blur');
        $('.money-text').trigger('blur');
        if (nameisok == false || phoneisok == false || moneyisok == false) {
            return false;
        }
        var vals = $("form").serialize();
        $(this).attr('disabled', true);
        var xhr = $.post($("#form_wgt").attr("action"), vals, function (data) {
            var inputObj = '';
            if (data.code == 0) {
                alert("您已预约成功");
                location.href = "/";
            } else if (data.code == 1) {
                $("#name_err").html(data.message);
                inputObj = $(".name-text");
            } else if (data.code == 2) {
                $("#mobile_err").html(data.message);
                inputObj = $(".phone-text");
            } else if (data.code == 3 || data.code == 4) {
                $("#money_err").html(data.message);
                inputObj = $(".money-text");
            }
            if (inputObj) {
                showInputBorder(inputObj, false);
            }
        });
        xhr.always(function () {
            $(this).attr('disabled', false);
        });

    });
});
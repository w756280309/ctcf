$(function() {
    $('.bind-check a').on('click', function() {
        $('.bank-mark').fadeIn();
        $('.bankIcon-box').fadeIn();
    });

    $('.bind-card').on('click', function() {
        $('.bank-mark').fadeIn();
        $('.bankIcon-box').fadeIn();
    });

    /*
     * 关闭窗口
     */
    $('.close').on('click', function() {
        $('.bank-mark').fadeOut();
        $('.bankIcon-box').fadeOut();
    });

    /*
     * 选择银行
     */
    var dataImg, dataBank;
    $('.bankIcon-inner li').on('click', function() {
        var index = $('.bankIcon-inner li').index(this);
        $('.bankIcon-inner li').removeClass('border-red');
        $('.bankIcon-inner li').eq(index).addClass('border-red');
        dataImg = $('.bankIcon-inner li').eq(index).attr('data-img');
        dataBank = $('.bankIcon-inner li').eq(index).attr('data-bank');
    });

    $('.bind-btn').hover(function() {
        $('.bind-btn').css({background: '#f41c11'});
    }, function() {
        $('.bind-btn').css({background: '#f44336'});
    });

    $('.bankIcon-btn').hover(function() {
        $('.bankIcon-btn').css({background: '#f41c11'});
    }, function() {
        $('.bankIcon-btn').css({background: '#f44336'});
    });

    $('.bankIcon-btn').on('click', function() {
        selectOneBank(dataImg, dataBank);
    });
    $(".bank-li-box").on('dblclick', function() {
        selectOneBank(dataImg, dataBank);
    });

    $('#card_no').blur(function() {
        var card_no = $(this).val();
        if (card_no === '') {
            $('.bind-card').hide();
            $('.bind-check').show();
            $('#bank_id').val('');
            $('#bank_name').val('');

            return false;
        }

        $.get("/user/userbank/checkbank", {card: card_no}, function(result) {
            if (result.code !== 0) {
                $('.bind-card').hide();
                $('.bind-check').show();
                error(result.message);
                $('#bank_id').val('');
                $('#bank_name').val('');

                return false;
            }

            if (result['bank_id'] !== '') {
                $('#bank_id').val(result['bank_id']);
                $('#bank_name').val(result['bank_name']);
            }

            if (result['bank_name'] === '') {
                $('#bank_id').val('');
                $('#bank_name').val('');

                return false;
            } else {
                $('.bind-icon img')[0].src = '/images/useraccount/bankicon/' + result['bank_id'] + '.png';
                $('.bind-bank').html(result['bank_name']);
                $('.bind-check').hide();
                $('.bind-card').show();
                //当银行卡名称成功发生改变时，应重新前台验证一下
                validate();
            }
        });
    });

    csrf = $("meta[name=csrf-token]").attr('content');
    $('#form').on('submit', function(e) {
        e.preventDefault();

        if (m == 1) {
            mianmi();
            return false;
        }

        if (validate()) {
            subForm();
        }
    });
})

function validate()
{
    if ('' === $.trim($('#card_no').val())) {
        error('银行卡号不能为空');
        return false;
    }

    var reg = /^[0-9]{16,19}$/;
    if (!reg.test($.trim($('#card_no').val()))) {
        error('你输入的银行卡号有误');
        return false;
    }

    if ('' === $('#bank_name').val()) {
        error('开户行不能为空');
        return false;
    }

    //前台验证无错误时，还原初始状态
    if ($('#card_no').hasClass('error-border')) {
        $('#card_no').removeClass('error-border');
    }
    $('#error').html('').hide();
    return true;
}

function subForm()
{
    var $form = $('#form');
    if ($form.data('submitting')) {
        return;
    }
    $form.data('submitting', true);
    var xhr = $.ajax({
        url: $form.attr('action'),
        data: $form.serialize(),
        type: 'POST',
        async: false
    });

    xhr.done(function(data)
    {
        var w = window.open(data.next);
        var url = data.next;
        if (url && !w) {
            location.href = url;
        } else if (url && w) {
            alertMessage('请在新打开的联动优势页面进行绑卡，绑卡完成前不要关闭该窗口。', '/user/userbank/mybankcard');
        }
    });

    xhr.fail(function(jqXHR)
    {
        var errMsg = jqXHR.responseText ? $.parseJSON(jqXHR.responseText).message
                : '未知错误，请刷新重试或联系客服';

        error(errMsg);
    });

    xhr.always(function()
    {
        $form.data('submitting', false);
    });
}

function error(desc)
{
    $('#card_no').addClass('error-border');
    $('#error').show();
    $('#error').html(desc);
}

function selectOneBank(dataImg, dataBank)
{
    var pre_bank_id = $('#bank_id').val();
    $('.bind-icon img')[0].src = '/images/useraccount/bankicon/' + dataImg + '.png';
    $('.bind-bank').html(dataBank);
    $('#bank_id').val(dataImg);
    $('#bank_name').val(dataBank);

    if ($('.bankIcon-inner .border-red').length > 0){
        $('.bind-check').hide();
        $('.bind-card').show();
    } else {
        $('.bind-check').show();
        $('.bind-card').hide();
    }
    $('.bank-mark').fadeOut();
    $('.bankIcon-box').fadeOut();

    //判断当前所选的银行是否与已选银行一致，若不一致，则将卡号置为空
    if ('' !== pre_bank_id && parseInt(pre_bank_id) !== parseInt(dataImg)) {
        $("#card_no").val('');
    }
}

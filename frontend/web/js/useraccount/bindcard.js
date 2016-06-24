$(function(){
   $('.bind-check a').on('click',function(){
       $('.bank-mark').fadeIn();
       $('.bankIcon-box').fadeIn();
   });
    $('.bind-card').on('click',function(){
        $('.bank-mark').fadeIn();
        $('.bankIcon-box').fadeIn();
    });
    $('.close').on('click',function(){
        $('.bank-mark').fadeOut();
        $('.bankIcon-box').fadeOut();
    });
    $('.bankIcon-inner li').on('click',function(){
        var index= $('.bankIcon-inner li').index(this);
        $('.bankIcon-inner li').removeClass('border-red');
        $('.bankIcon-inner li').eq(index).addClass('border-red');
        var dataImg=$('.bankIcon-inner li').eq(index).attr('data-img');
        var dataBank=$('.bankIcon-inner li').eq(index).attr('data-bank');
        $('.bind-icon img')[0].src='/images/useraccount/bankicon/'+dataImg+'.png';
        $('.bind-bank').html(dataBank);
        $('#bank_id').val(dataImg);
        $('#bank_name').val(dataBank);
    });
    $('.bind-btn').hover(function(){
        $('.bind-btn').css({background:'#f41c11'});
    },function(){
        $('.bind-btn').css({background:'#f44336'});
    });
    $('.bankIcon-btn').hover(function(){
        $('.bankIcon-btn').css({background:'#f41c11'});
    },function(){
        $('.bankIcon-btn').css({background:'#f44336'});
    });
    $('.bankIcon-btn').on('click',function(){
        $('.bind-check').hide();
        $('.bind-card').show();
        $('.bank-mark').fadeOut();
        $('.bankIcon-box').fadeOut();
    });

    $('#card_no').blur(function() {
        var card_no = $(this).val();
        if(card_no === '') {
            $('.bind-card').hide();
            $('.bind-check').show();
            $('#bank_id').val('');
            $('#bank_name').val('');

            return false;
        }

        $.get("/user/userbank/checkbank", {card: card_no}, function (result) {
            if(result.code !== 0) {
                $('.bind-card').hide();
                $('.bind-check').show();
                error(result.message);
                $('#bank_id').val('');
                $('#bank_name').val('');

                return false;
            }

            if(result['bank_id'] !== '') {
                $('#bank_id').val(result['bank_id']);
                $('#bank_name').val(result['bank_name']);
            }

            if(result['bank_name'] === '') {
                $('#bank_id').val('');
                $('#bank_name').val('');

                return false;
            } else {
                $('.bind-icon img')[0].src='/images/useraccount/bankicon/'+result['bank_id']+'.png';
                $('.bind-bank').html(result['bank_name']);
                $('.bind-check').hide();
                $('.bind-card').show();
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

    return true;
}

function subForm()
{
    var $form = $('#form');
    if ($form.data('submitting')) {
        return;
    }
    $form.data('submitting', true);
    var xhr = $.post(
        $form.attr('action'),
        $form.serialize()
    );

    xhr.done(function (data) {
        window.location.href = data.next;
    });

    xhr.fail(function (jqXHR) {
        var errMsg = jqXHR.responseText ? $.parseJSON(jqXHR.responseText).message
            : '未知错误，请刷新重试或联系客服';

        error(errMsg);
    });

    xhr.always(function () {
        $form.data('submitting', false);
    });
}

function error(desc)
{
    $('#card_no').addClass('error-border');
    $('#error').show();
    $('#error').html(desc);
}
$("input").bind('keypress', function(e) {
    if (e.keyCode === 13) {
        $('#code_submit_button').click();
    }
});
function initCodeBox () {
    $("#code").val("");
    $(".code_err").html("");
    $("#success-refer").html("");
    $(".code-success").hide();
    $(".code-content").show();
    $("#code_submit_button").html("立即兑换");
    $(".code-bottom").removeClass("continue-button");
}
$(function(){
    $("#couponcode").click(function(){
        $(".code-mark").show();
        $(".couponcode-box").show();
    });
    $("#couponcode-box .close").click(function(){
        $(".code-mark").hide();
        $("#couponcode-box").hide();
        initCodeBox();
        location.reload();
    });
    $("#code_submit_button").click(function(){
        if ($(".code-bottom").hasClass("continue-button")) {
            initCodeBox();
            return false;
        }
        if ($(this).hasClass("already")) {
            return false;
        }
        var code = $("#code").val();
        if (code == '') {
            $(".code_err").html("请输入兑换码");
            return false;
        }
        if (code.length !== 16) {
            $(".code_err").html("兑换码有误，请重新输入");
            return false;
        }
        var reg = /^[a-zA-Z0-9]{16}$/;
        if (!reg.test(code)) {
            $(".code_err").html("兑换码有误，请重新输入");
            return false;
        }
        $(".code_err").html("");
        $(this).addClass("already");
        var form = $("#code-forms");

        var xhr = $.ajax({
            type: 'POST',
            url: form.attr("action"),
            data: form.serialize(),
            dataType: 'json'
        });

        xhr.done(function(data) {
            $("#code_submit_button").removeClass("already");
            if (data.requireLogin === 1) {
                location.href = "/site/login";
            }
            if (data.code > 0) {
                if (data.code === 1) {
                    $("#code").val('');
                }
                $(".code_err").html(data.message);
            } else {
                $(".code-bottom").addClass("continue-button");
                $(".code_err").html("");
                $("#code").val("");
                $(".code-content").hide();
                $("#success-refer").html(data.data);
                $(".code-success").show();
                $("#code_submit_button").html("继续兑换");
            }
        });

        xhr.fail(function(jqXHR) {
            $("#code_submit_button").removeClass("already");
        });
    })
})
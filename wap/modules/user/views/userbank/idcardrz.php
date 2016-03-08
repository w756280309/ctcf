<?php
$this->title="开通资金托管账户";
$this->registerJsFile('/js/common.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
?>
<link rel="stylesheet" href="/css/bind.css"/>
<link rel="stylesheet" href="/css/chongzhi.css"/>
<link rel="stylesheet" href="/css/base.css"/>

    <div style="height: 10px"></div>
    <!--交易密码-->
    <form method="post" class="cmxform" id="form" action="/user/userbank/idcardrz" data-to="1">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-1">真实姓名</div>
            <div class="col-xs-9 col-sm-8"><input type="text" id="real_name" name='User[real_name]' placeholder="请输入您的真实姓名"/></div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <div class="row kahao">
            <div class="hidden-xs col-sm-1"></div>
            <div class="col-xs-3 col-sm-1">身份证号</div>
            <div class="col-xs-9 col-sm-8"><input type="text" id="idcard" name='User[idcard]' placeholder="请输入您的身份证号"/></div>
            <div class="hidden-xs col-sm-1"></div>
        </div>
        <!--提交按钮-->
        <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6 login-sign-btn">
                <input id="idcardbtn" class="btn-common btn-normal" name="signUp" type="button" value="开 通">
            </div>
            <div class="col-xs-3"></div>
        </div>
    </form>
    <!-- 卡号弹出框 start  -->
    <div class="error-info">您输入身份证少于18位</div>
    <!-- 开好弹出框 end  -->
    <script type="text/javascript">
    var csrf;
    function validateform() {
        $(this).addClass("btn-press").removeClass("btn-normal");
        if($('#real_name').val() == '') {
            toast('姓名不能为空');
            $(this).removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        if($('#idcard').val() == '') {
            toast('身份证号不能为空');
            $(this).removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        if($('#idcard').val().length !== 18) {
            toast('身份证暂只支持18位');
            $(this).removeClass("btn-press").addClass("btn-normal");
            return false;
        }
        return true;
    }
    $(function(){
       var err = '<?= $code ?>';
       var mess = '<?= $message ?>';
       var tourl = '<?= $tourl ?>';
       if(err === '1') {
           toast(mess, function() {
                if (tourl !== '') {
                    location.href = tourl;
                }
           });
       }

       csrf = $("meta[name=csrf-token]").attr('content');
       $('#idcardbtn').bind('click',function(){
            $(this).addClass("btn-press").removeClass("btn-normal");
            subForm();
            $(this).removeClass("btn-press").addClass("btn-normal");
       });

    })

    function subForm()
    {
        if(!validateform()){
           return false;
        }
        var $form = $('#form');
        $('#idcardbtn').attr('disabled', true);
        var xhr = $.post(
            $form.attr('action'),
            $form.serialize()
        );

        xhr.done(function(data) {
            $('#idcardbtn').attr('disabled', false);
            if (0 === data.code) {
                alertTrue(function() {
                    location.href = data.tourl;
                });
            } else {
                toast(data.message, function() {
                    if (typeof data.tourl !== 'undefined') {
                        location.href = data.tourl;
                    }
                });
            }

        });

        xhr.fail(function(jqXHR) {
            var errMsg = jqXHR.responseJSON && jqXHR.responseJSON.message
                ? jqXHR.responseJSON.message
                : '未知错误，请刷新重试或联系客服';

            toast(errMsg);
            $('#idcardbtn').attr('disabled', false);
        });
    }

    function alertTrue(trued) {
        var chongzhi = $('<div class="mask" style="display: block"></div><div class="bing-info show"> <div class="bing-tishi">开通成功</div> <p class="tishi-p" style="line-height: 20px;">支付密码将会以短信的形式发送到您的手机上,请注意查收并妥善保存。支付密码为6位随机数,可根据短信内容修改密码</p > <div class="bind-btn"> <span class="true">下一步</span> </div> </div>');
        $(chongzhi).insertAfter($('form'));
        $('.bing-info').on('click', function () {
            $(chongzhi).remove();
            trued();
        })
    }
    </script>


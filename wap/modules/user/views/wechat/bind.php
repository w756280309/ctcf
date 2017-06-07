<?php

$this->title = '温都金服_绑定服务号';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/weixin-bound/css/bind.css?v=1">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>

<div class="flex-content">
    <div class="top-box">
        <p class="p1">为什么要绑定微信？</p>
        <p class="p2" style="margin-top: 0.333rem;">一键绑定账号，随时查看账户信息，</p>
        <p class="p2">实时接收交易信息、计息、福利提醒。</p>
        <img src="<?= FE_BASE_URI ?>wap/weixin-bound/images/weixin_bind.png" alt="">
    </div>
    <form id="form" action="/user/wechat/do-bind" method="post">
        <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="form-box">
            <div class="input-box">
                <label for="phone" class="lf">手机号码</label>
                <input type="number" class="phone lf" maxlength="11" id="mobile" name="mobile" placeholder="请输入已注册的手机号" value="<?= $user ? $user->getMobile() : '' ?>">
            </div>
            <div class="input-box">
                <label for="password" class="lf">登录密码</label>
                <input type="password" class="password lf" name="password" id="password" placeholder="请输入登录密码">
                <span class="yincang"></span>
            </div>
        </div>
    </form>
    <a href="javascript:void(0)" class="btn-press queren">立 即 绑 定</a>
    <p class="tel">客服电话：<a href="tel://400-101-5151">400-101-5151</a></p>
</div>

<script>
    $(function () {
        $('.input-box span').on('click', function () {
            if ($('.password').attr('type') === 'password') {
                $('.password').attr({'type': 'text'});
                this.setAttribute("class","xianshi" );
            } else {
                $('.password').attr({'type': 'password'});
                this.setAttribute("class","yincang" );
            }
        });

        var allowClick = true;

        $('.queren').on('click', function (e) {
            e.preventDefault;

            if(!allowClick) {
                return;
            }

            if (!validateMobile()) {
                allowClick = true;
                return;
            }

            var xhr = $.post('/user/wechat/do-bind', $('#form').serialize());
            allowClick = false;

            xhr.done(function(data) {
                if (data.code === 0) {
                    toastCenter(data.message, function () {
                        location.href = '/user/wechat/bind-success';
                    });
                }

                allowClick = true;
            });

            xhr.fail(function(jqXHR) {
                if (400 === jqXHR.status && jqXHR.responseText) {
                    var resp = $.parseJSON(jqXHR.responseText);

                    toastCenter(resp.message, function () {
                        if ('信息获取失败，请退出重试' === resp.message) {
                            location.href = '';
                        }

                        allowClick = true;
                    });
                } else {
                    toastCenter('系统繁忙，请稍后重试！', function () {
                        allowClick = true;
                    });
                }
            });
        });
    })

    function validateMobile() {
        var mobile = $('#mobile').val();

        if ('' === mobile) {
            toastCenter('手机号不能为空');
            return false;
        }

        var reg = /^0?1[3|4|5|6|7|8][0-9]\d{8}$/;
        if (!reg.test(mobile)) {
            toastCenter('手机号格式错误');
            return false;
        }

        if ('' === $('#password').val()) {
            toastCenter('密码不能为空');
            return false;
        }

        return true;
    }
</script>

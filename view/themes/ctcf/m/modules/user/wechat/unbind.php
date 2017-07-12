<?php

use common\utils\StringUtils;

$this->title = '楚天财富_解除绑定';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/weixin-bound/css/unbind.css">
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>

<div class="flex-content">
    <img src="<?= FE_BASE_URI ?>wap/weixin-bound/images/unbind.png" alt="" class="picture">
    <p class="remind">解除绑定账号：<?= StringUtils::obfsMobileNumber($user->getMobile()) ?>,</p>
    <p class="remind">您将无法在微信及时收到该账号的交易提醒。</p>
    <p class="detail" style="margin-top: 1.67rem;">在官方微信，“马上赚钱”</p>
    <p class="detail">点击“账号绑定”即可再次绑定账号</p>
    <a href="javascript:void(0)" class="queren">解 除 绑 定</a>
    <p class="tel">客服电话：<a href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></p>
</div>

<script type="text/javascript">
    var allowClick = true;

    $('.queren').on('click', function (e) {
        e.preventDefault;

        if(!allowClick) {
            return;
        }

        var xhr = $.get('/user/wechat/do-unbind');
        allowClick = false;

        xhr.done(function(data) {
            if (data.code === 0) {
                toastCenter(data.message, function () {
                    location.href = '/user/wechat/unbind-success';
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
</script>

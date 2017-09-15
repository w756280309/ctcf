<?php

$this->title = '注册成功';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>pc/signup-consult/css/index.css">

<div class="signup-consult">
    <div class="consult-content">
        <div class="consult-top"><span class="icon"></span>恭喜您注册成功！</div>
        <ol class="ucenter">
            <li><span class="active-txt">288元</span>代金券已经发放至您的账户中心</li>
            <li><span class="color-txt">开通资金托管</span>账户后即可使用</li>
        </ol>
        <div class="box">
            <?php if(!empty($affArr)) {?>
            <p class="media"><?= $fromNb ? '推荐渠道' : '推荐媒体'?>:
                <select name="channel" id="channel">
                    <?php foreach ($affArr as $key => $val) { ?>
                        <option value="<?= $key ?>" <?= $key === $affiliatorId ? 'selected' : '' ?>><?= $val ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" class="lastChannel" name="lastChannel" value="-1">
            </p>
            <?php }?>
            <a href="/user/identity" class="huifu-open">立即开通</a>
        </div>
    </div>
</div>
<div class="clear"></div>

<script>
    $(function() {
        $("#channel").on("change", function() {
            var cid = parseInt($(this).val());
            if (cid === parseInt($('.lastChannel').val())) {
                return false;
            }
            $('.lastChannel').val(cid);
            $.get('/site/add-affiliator?id=' + cid);
        });
    });
</script>
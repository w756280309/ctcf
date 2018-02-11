<?php

$this->hideHeaderNav = true;
$this->title = '楚天财富注册成功页面';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/register/registerSucc.css?v=20170907">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>

<div class="container flex-content">
    <?php if (!defined('IN_APP')) { ?>
        <div class="registertop f19">注册成功</div>
    <?php } ?>
    <div class="registermildle f17">
        <img src="<?= FE_BASE_URI ?>wap/register/images/tick_01.png" alt="">
        <p>恭喜您注册成功！</p>
    </div>
    <ul class="registerlist f15">
        <li>• <i style="color: #ff6707;font-style: normal;">888元</i>红包已经发放至您的账户中心</li>
        <li>• <i style="color: #ff532d;font-style: normal;">开通资金托管</i>账户后即可使用</li>
    </ul>
    <?php if(!empty($affArr)) {?>
    <div class="registerselect f15">
        <span><?= $fromNb ? '推荐渠道' : '推荐媒体'?>:</span>
        <select name="channel" id="channel" style="max-width:3.1rem;">
            <?php foreach ($affArr as $key => $val) { ?>
                <option value="<?= $key ?>" <?= $key === $affiliatorId ? 'selected' : '' ?>><?= $val ?></option>
            <?php } ?>
        </select>
        <input type="hidden" class="lastChannel" name="lastChannel" value="-1">
    </div>
    <?php }?>
    <a class="f16 open" href="/user/identity">实名认证</a>
    <a class="f16 stroll" href="/user/user">先去逛逛</a>
</div>
<script>
    $(function(){
        FastClick.attach(document.body);
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

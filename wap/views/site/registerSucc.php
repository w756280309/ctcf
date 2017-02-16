<?php

$this->hideHeaderNav = true;
$this->title = '温度金服注册成功页面';

?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/register/css/registerSucc.css?v=1.0">
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
        <li>• 288元代金券已经发放至您的账户中心</li>
        <li>• 开通资金托管账户后即可使用</li>
    </ul>
    <div class="registerselect f15">
        <span>推荐媒体:</span>
        <select name="channel" id="channel" style="max-width:3.1rem;">
            <?php foreach ($affArr as $key => $val) { ?>
                <option value="<?= $key ?>" <?= $key === $affiliationId ? 'selected' : '' ?>><?= $val ?></option>
            <?php } ?>
        </select>
        <input type="hidden" class="lastChannel" name="lastChannel" value="-1">
    </div>
    <a class="f16 open" href="/user/identity">立即开通</a>
    <a class="f16 stroll" href="/?mark=170120">先去逛逛</a>
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
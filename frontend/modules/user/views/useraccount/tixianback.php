<?php
?>
<div class="container" align="center">
    <div class="main" style="width:500px;">
            <div class="modal-body text-center">
                <p style='color: red; font-size: 20px'><?= ($flag === 'succ')?"提现申请成功":"提现申请失败" ?></p>

                <div style="margin: 20px auto; width: 90px; height: 90px;"><img src="<?= ($flag === 'succ')?'/images/true.png':'/images/false.png' ?>"/></div>

                <p><button type="button" class="btn btn-primary" onclick="window.location.href='/user/useraccount/accountcenter'">返回账户</button></p>

                <p>* T+1个工作日到账，节假日顺延</p>
            </div>
    </div>
</div>


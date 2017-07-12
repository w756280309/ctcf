<?php
?>
<div class="container" align="center">
    <div class="main" style="width:500px;">
            <div class="modal-body text-center">
                <p style='color: red; font-size: 20px'>用户已锁定</p>

                <div style="margin: 20px auto; width: 90px; height: 90px;"><img src="<?= ASSETS_BASE_URI ?>images/false.png"/></div>

                <p>请联系客服，电话：<?= Yii::$app->params['platform_info.contact_tel'] ?></p>
            </div>
    </div>
</div>
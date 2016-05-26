<?php
?>
<div class="container" align="center">
    <div class="main" style="width:500px;">
            <div class="modal-body text-center">
                <p style='color: red; font-size: 20px'>充值申请失败</p>

                <div style="margin: 20px auto; width: 90px; height: 90px;"><img src="/images/false.png"/></div>

                <p><button type="button" class="btn btn-primary" onclick="window.location.href='/user/useraccount/accountcenter'">返回账户</button></p>
                
                <p>客服电话：<?= Yii::$app->params['contact_tel'] ?></p>
            </div>
    </div>
</div>
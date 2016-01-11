<?php

use yii\web\View;

$_js = <<<'JS'
$(function() {
    var $modal = $('#bind-card-modal').modal();
    $modal.modal('show');
})
JS;
$this->registerJs($_js, View::POS_END, 'body_close');

?>
<div class="container">
    <div class="main">
        <div class="section">
            <div class="section-header">
                <h2>账户资金</h2>
            </div>

            <div class="stats row">
                <div class="col-sm-4">累计收益 <span class="balance profit"><?= $model->profit_balance ?></span> 元</div>
                <div class="col-sm-4">账户总额 <span class="balance"><?= $model->account_balance ?></span> 元</div>
                <div class="col-sm-4">可用余额 <span class="balance"><?= $model->available_balance ?></span> 元</div>
            </div>

            <div class="actions">
                <a class="btn btn-primary" href="/user/recharge/recharge">充值</a>
                <a class="btn btn-default" href="/user/useraccount/tixian">提现</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bind-card-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p>您当前的账户未绑定银行卡<br>请先访问移动端网站完成绑卡操作</p>

                <div style="margin: 20px auto; width: 160px; height: 160px; background-color: #eee;"></div>

                <p><button type="button" class="btn btn-primary" data-dismiss="modal">我知道了</button></p>
            </div>
        </div>
    </div>
</div>

<?php

use yii\web\View;

$this->title = '账户中心-温都金服';

$_js = <<<'JS'
$(function() {
    var $modal = $('#bind-card-modal').modal();
    $modal.modal('show');

    $("#tixian").bind('click', function() {
        var $modal = $('#bind-card-modal').modal();
        $modal.modal('show');
    })
})
JS;

if ($errflag) {
    $this->registerJs($_js, View::POS_END, 'body_close');
}

?>
<div class="container">
    <div style="margin-top: 30px">
        <img class="headpic" src="/images/headpic.png"><?= $username?"欢迎您，".$username:"" ?>
    </div>
    <div class="main dash" style="margin-top: 0px">
        <div class="page-heading">
            <h2>账户资金</h2>
        </div>

        <div class="stats row">
            <div class="col-sm-4">累计收益 <span class="balance profit"><?= number_format($model->profit_balance, 2) ?></span> 元</div>
            <div class="col-sm-4">账户总额 <span class="balance"><?= number_format($model->account_balance, 2) ?></span> 元</div>
            <div class="col-sm-4">可用余额 <span class="balance"><?= number_format($model->available_balance, 2) ?></span> 元</div>
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="/user/recharge/recharge">充值</a>
            <a class="btn btn-default" id='tixian' href="<?= $errflag?'javascript:void(0)':'/user/useraccount/tixian' ?>">提现</a>
        </div>
    </div>
</div>

<div class="modal fade" id="bind-card-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p><?= $errmess ?></p>

                <p style="font-size: 10px; color: #c0c0c0;">请先访问移动端网站完成相关操作</p>

                <div style="margin: 20px auto; width: 160px; height: 160px; background-color: #eee;"></div>

                <p><button type="button" class="btn btn-primary" data-dismiss="modal">我知道了</button></p>
            </div>
        </div>
    </div>
</div>

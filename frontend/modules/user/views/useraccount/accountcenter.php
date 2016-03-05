<?php

use yii\web\View;

$this->title = '账户中心-温都金服';

$_js = <<<'JS'
$(function() {
    var $modal = $('#bind-card-modal').modal();
    $modal.modal('show');
})
JS;

$_js2 = <<<'JS'
$(function() {
    $('#rechargebtn').bind('click', function() {
        $.get('/user/useraccount/recharge-validate', function(data) {
            if (1 === data.code) {
                var $modal = $('#bind-card-modal').modal();
                $modal.modal('show');
            } else {
                window.location.href = '/user/recharge/init';
            }
        });
    });
    $('#drawbtn').bind('click', function() {
        $.get('/user/useraccount/draw-validate', function(data) {
            if (1 === data.code) {
                var $modal = $('#bind-card-modal').modal();
                $modal.modal('show');
            } else {
                window.location.href = '/user/draw/tixian';
            }
        });
    });
})
JS;

if ($errflag) {
    $this->registerJs($_js, View::POS_END, 'body_close');
}

$this->registerJs($_js2, View::POS_END, 'body_close2');
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
            <a class="btn btn-primary" id="rechargebtn" href="javascript:void(0)">充值</a>
            <a class="btn btn-default" id='drawbtn' href="javascript:void(0)"s>提现</a>
        </div>
    </div>
</div>

<div class="modal fade" id="bind-card-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p><?= $errmess ?></p>

                <p style="font-size: 10px; color: #c0c0c0;">请先访问移动端网站完成相关操作</p>

                <div style="margin: 20px auto; width: 160px; height: 160px; background-image: url('/images/orcode.jpg'); background-size: 160px;"></div>

                <p><button type="button" class="btn btn-primary" data-dismiss="modal">我知道了</button></p>
            </div>
        </div>
    </div>
</div>

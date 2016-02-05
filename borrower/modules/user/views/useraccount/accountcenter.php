<?php
$this->title = '账户中心-温都金服';
?>
<div class="container">
    <div style="margin-top: 30px">
        <img class="headpic" src="/images/headpic.png">欢迎您
    </div>
    <div class="main dash" style="margin-top: 0px">
        <div class="page-heading">
            <h2>账户资金</h2>
        </div>

        <div class="stats row">
            <div class="col-sm-4">可用余额 <span class="balance"><?= number_format($model->available_balance, 2) ?></span> 元</div>
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="/user/recharge/recharge">充值</a>
            <a class="btn btn-default" id='tixian' href="<?= $errflag ? 'javascript:void(0)' : '/user/useraccount/tixian' ?>">提现</a>
        </div>
    </div>
</div>

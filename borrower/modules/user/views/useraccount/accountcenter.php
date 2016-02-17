<?php
$this->title = '账户中心-温都金服';
?>
<style>
    .headpic {
        width: 30px;
        height: 30px;
        margin: 10px;
    }
    .index_pic {
        width: 50px;
        height: 50px;
        position: absolute;
        top: 35px;
        right:62px;
        background-repeat: no-repeat;
    }
</style>
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
            <a class="btn btn-primary" href="/user/recharge/init">充值</a>
        </div>
    </div>
</div>

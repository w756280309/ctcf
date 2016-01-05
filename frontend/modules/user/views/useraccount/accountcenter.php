<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
    <body>
        <?php $this->beginBody() ?>
        <?= "累计收益： ".$model->profit_balance."元" ?><br>
        <?= "账户总额： ".$model->account_balance."元" ?><br>
        <?= "可用余额： ".$model->available_balance."元" ?><br>
        <a href="/user/recharge/recharge">充值</a>
        <a href="/user/useraccount/tixian">提现</a>
        <?php $this->endBody() ?>

    </body>
</html>
<?php $this->endPage() ?>

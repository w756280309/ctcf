<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-2-7
 * Time: 下午6:44
 */

$this->title = "解绑银行卡结果";
$this->backUrl = false;

$this->registerJsFile('', ['depends' => 'yii\web\YiiAsset', 'position' => 1]);
?>
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/mycard.css">

    <div class="row" id='bind-box'>
        <div class="col-xs-12">
            <?php if (1 === $result['code']) { ?>
                <div>银行卡解绑成功</div>
            <?php } else { ?>
                <div>银行卡解绑失败</div>
            <?php } ?>
        </div>
    </div>
    <div class="row" id='bind-true'>
        <div class="col-xs-12">
            <?php if (1 === $result['code']) { ?>
                <img src="/images/bind-card-true.png" alt="银行卡解绑成功">
            <?php } else { ?>
                <img src="/images/bind-card-false.png" alt="银行卡解绑失败">
            <?php } ?>
        </div>
    </div>
    <div class="row daojishi">
        <div class="col-xs-12">
            <?php if (1 === $result['code']) { ?>
                <div><span>5秒</span>后前往关闭页面</div>
            <?php } else { ?>
                <div>&nbsp;</div>
            <?php } ?>
        </div>
    </div>
<?php if (1 === $result['code']) { ?>
    <div class="row" id='bind-close1'>
        <div class="col-xs-3"></div>
        <div class="col-xs-6">
            <a href="/user/user/index" class="bind-close1">关闭</a>
        </div>
        <div class="col-xs-3"></div>
    </div>
    <script>
        $(function () {
            var num = 5;
            var t = setInterval(function () {
                num--;
                $('.daojishi .col-xs-12 span').html(num + '秒');
                if (num == 0) {
                    clearInterval(t);
                    location.href = '/user/user/index';
                }
            }, 1000);
        })
    </script>
<?php } else { ?>
    <div class="row" id='bind-close1'>
        <div class="col-xs-3"></div>
        <div class="col-xs-6">
            <a href="/user/bank/card" class="bind-close1">重新解绑</a>
        </div>
        <div class="col-xs-3"></div>
    </div>
<?php } ?>
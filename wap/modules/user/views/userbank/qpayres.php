<?php
$this->title = ('success' === $ret) ? "充值成功" : "充值失败";
$this->backUrl = false;
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css">

<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <div>充值成功</div>
        <?php } else { ?>
        <div>充值失败</div>
        <?php } ?>
    </div>
</div>
<div class="row" id='bind-true'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-true.png" alt="">
        <?php } else { ?>
        <img src="<?= ASSETS_BASE_URI ?>images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>
<div class="row daojishi">
     <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <?php if ('' !== $from_url) { ?>
                <div><span>5秒</span>后回到购买页</div>
             <?php } else { ?>
                 <div><span>5秒</span>后回到账户</div>
             <?php } ?>
        <?php } else { ?>
        <div>请联系客服: <?= Yii::$app->params['contact_tel'] ?></div>
        <?php } ?>
     </div>
</div>
<?php if ('success' === $ret) { ?>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <?php if ('' !== $from_url) { ?>
        <a href="javascript:void(0)" onclick="history.go(-4)" class="bind-close1">返回购买页</a>
        <?php } else { ?>
            <a href="/user/user/index" class="bind-close1">返回账户</a>
        <?php } ?>
    </div>
    <div class="col-xs-4"></div>
</div>
<script>
    $(function(){
        var num=5;
        var url = "<?= $from_url ? \yii\helpers\Html::encode($from_url) : '/user/user/index' ?>";
        var t=setInterval(function(){
            num--;
            $('.daojishi .col-xs-12 span').html(num+'秒');
            if(num==0){
                clearInterval(t);
                if (url === '/user/user/index') {
                    location.href = url;
                } else {
                    history.go(-4);
                }
            }
        },1000);
    })
</script>
<?php } else { ?>
<div class="row" id='bind-close1'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <?php if ('' !== $from_url) { ?>
            <a href="<?= \yii\helpers\Html::encode($from_url) ?>" class="bind-close1">返回购买页</a>
        <?php } else { ?>
            <a href="/user/user/index" class="bind-close1">返回账户</a>
        <?php }?>

    </div>
    <div class="col-xs-4"></div>
</div>
<div class="row" id='bind-close2'>
    <div class="col-xs-4"></div>
    <div class="col-xs-4">
        <a href="/user/userbank/recharge<?= $from_url ? '?from='.\yii\helpers\Html::encode($from_url) : '' ?>" class="bind-close1">继续充值</a>
    </div>
    <div class="col-xs-4"></div>
</div>
<?php } ?>

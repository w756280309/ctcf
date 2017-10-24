<?php

$this->title = ('success' === $ret) ? '购买成功' : '购买失败';
$this->backUrl = false;
$cookies = Yii::$app->request->cookies;
if (isset($cookies['pointPopUp'])) {
    $popUpOnce = $cookies['pointPopUp']->value;
}
?>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/setting.css?v=20160714">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/qiandao/css/index.css?v=20170711">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<style>
    .container{
        height: 100vh;
    }
    #bind-close1{
        margin-top: 0.8rem;
    }
</style>
<div class="row" id='bind-box'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <div>购买成功</div>
        <?php } else { ?>
            <div>购买失败</div>
        <?php } ?>
    </div>
</div>

<div class="row" id='bind-true'>
    <div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
            <img src="<?= ASSETS_BASE_URI ?>images/bind_true.png" alt="">
        <?php } else { ?>
            <img src="<?= ASSETS_BASE_URI ?>images/bind-false.png" alt="">
        <?php } ?>
    </div>
</div>

<div class="row daojishi" id='bind-close1'>
    <div class="col-xs-1"></div>
    <div class="col-xs-5">
        <?php if ('success' === $ret) { ?>
            <a href="javascript:void(0)" onclick="location.replace('/user/user/orderdetail?id=<?= $order->id ?>')" class="bind-close1">查看详情</a>
        <?php } else { ?>
            <a href="javascript:void(0)" onclick="history.go(-1)" class="bind-close1">重新购买</a>
        <?php } ?>
    </div>
    <div class="col-xs-5">
        <a href="/?_mark=1" class="bind-close1">回到首页</a>
    </div>
    <div class="col-xs-1"></div>

    <div class="col-xs-12 page_padding">
        <?php if ('success' === $ret) { ?>
            <div>绑定公众号<font color="#999999">（温都金服全拼）</font>: "<font color="#FF8000">wendujinfu</font>"</div>
            <div>可以及时了解自己的收益，还送10个积分哦</div>
        <?php } else { ?>
            <div>遇到问题请联系客服，电话：<a class="contact-tel" href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></div>
        <?php } ?>
    </div>

    <?php if ('success' === $ret) { ?>
        <?php $backUrl = urlencode(Yii::$app->request->hostInfo.'/user/user') ?>
        <div class="checkin"><a href="/user/checkin?back_url=<?= $backUrl ?>"><img src="<?= ASSETS_BASE_URI ?>images/checkin.jpg" alt=""></a></div>
    <?php } ?>
</div>

<?php if ('success' === $ret) { ?>
    <!--添加签到蒙层-->
    <div class="mask"></div>
    <div class="pomp">
        <img src="<?= FE_BASE_URI ?>wap/point/img/point-header.png" alt="">
        <p class="pomp-time"><i></i>恭喜您获得<span></span><i></i></p>
        <p class="pomp-points"><span id="pomp-point"><?= $incrPoints ?></span>积分<i id="pomp-coupon"></i></p>
        <p>本次投资奖励</p>
        <p style=""><a href="/mall/point/list" style="background-color: transparent; color: #6A7FA6; font-size: 0.27rem;">查看积分</a></p>
        <a href="javascript:closePomp();">关闭</a>
    </div>
<?php } ?>

<script>
    $(function () {
        if ("<?= isset($popUpOnce) ?>") {
            $(".mask, .pomp").show();
            "<?php Yii::$app->response->cookies->remove('pointPopUp') ?>"    //投资成功后弹出框弹出一次后销毁此cookie
        }
    })

    function closePomp() {
        $(".mask, .pomp").hide();
    }
</script>
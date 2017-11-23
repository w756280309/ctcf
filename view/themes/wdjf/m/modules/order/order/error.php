<?php

$this->title = '购买结果';
$this->backUrl = false;
$cookies = Yii::$app->request->cookies;
if (isset($cookies['pointPopUp'])) {
    $popUpOnce = $cookies['pointPopUp']->value;
}
?>
<!--<link rel="stylesheet" href="--><? //= ASSETS_BASE_URI ?><!--css/setting.css?v=20160714">-->
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/qiandao/css/index.css?v=20170711">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<style>
	body {
		background-color: #f6f6f6;
	}

	.top-part {
		width: 100%;
		padding-top: 1.42666667rem;
		padding-bottom: 0.97333333rem;
		background-color: #fff;
	}

	.top-part #bind-true div img {
		display: block;
		width: 1.58666667rem;
		margin: 0 auto 0.29333333rem;
	}

	.top-part #bind-box div div {
		height: 0.37333333rem;
		line-height: 0.37333333rem;
		font-size: 0.37333333rem;
		color: #4e545a;
		text-align: center;
		margin-bottom: 1.13333333rem;
	}

	.top-part #bind-button {
		width: 100%;
		padding: 0 8.3333%;
		box-sizing: border-box;
	}

	.top-part #bind-button a {
		display: block;
		width: 45.16%;
		height: 1.12rem;
		line-height: 1.12rem;
		font-size: 0.45333333rem;
		border: 1px solid #ff6058;
		-webkit-border-radius: 4px;
		-moz-border-radius: 4px;
		border-radius: 4px;
		text-align: center;
	}

	.top-part #bind-button a.lf {
		background-color: #fff;
		color: #ff6058;
	}

	.top-part #bind-button a.rg {
		background-color: #ff6058;
		color: #fff;
	}

	.bottom-part {
		width: 100%;
		margin-top: 0.29333333rem;
	}

	.bottom-part .qiandao-box {
		height: 2.94666667rem;
	}

	.bottom-part .qiandao-box img {
		width: 100%;
		height: 100%;
	}

	.bottom-part .bind-box {
		display: block;
		width: 100%;
	}

	.bottom-part div {
		font-size: 0.3333rem;
		color: #4e545a;
		text-align: center;
	}

	.bottom-part div a {
		color: #ff6058;
	}

	.container {
		height: 100vh;
	}

	#bind-close1 {
		margin-top: 0.8rem;
	}


</style>
<div class="top-part">
	<div class="row" id='bind-true'>
		<div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
					<img src="<?= ASSETS_BASE_URI ?>images/icon_bidResult_success.png" alt="">
        <?php } else { ?>
					<img src="<?= ASSETS_BASE_URI ?>images/icon_bidResult_fail.png" alt="">
        <?php } ?>
		</div>
	</div>

	<div class="row" id='bind-box'>
		<div class="col-xs-12">
        <?php if ('success' === $ret) { ?>
					<div>购买成功</div>
        <?php } else { ?>
					<div>购买失败</div>
        <?php } ?>
		</div>
	</div>

	<div id="bind-button" class="clearfix">
		<a href="/?_mark=1" class="lf">回到首页</a>
      <?php if ('success' === $ret) { ?>
				<a href="javascript:void(0)" onclick="location.replace('/user/user/orderdetail?id=<?= $order->id ?>')"
				   class="rg">查看详情</a>
      <?php } else { ?>
				<a href="javascript:void(0)" onclick="history.go(-1)" class="rg">重新购买</a>
      <?php } ?>
	</div>
</div>

<div class="bottom-part">
    <?php if ('success' === $ret) { ?>
			<a href="/user/checkin" class="qiandao-box"><img
					src="<?= ASSETS_BASE_URI ?>images/bg_qiandao.png" alt=""></a>
			<a href="/system/system/setting" style="display: block"><img src="<?= ASSETS_BASE_URI ?>images/bg_bind.png" alt="" class="bind-box"></a>
    <?php } else { ?>
			<div>遇到问题请联系客服，电话：<a class="contact-tel"
			                     href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a>
			</div>
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
		<p style=""><a href="/mall/point/list" style="background-color: transparent; color: #6A7FA6; font-size: 0.27rem;">查看积分</a>
		</p>
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
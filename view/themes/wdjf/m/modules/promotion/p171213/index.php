<?php

$this->title  = '网银充值福利';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/internet-recharge/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/layer_mobile/layer.js"></script>
<div class="flex-content">
	<div class="top-part"></div>
	<div class="middle-part">
		<p class="recharge-number">已通过网银充值：<span class="notLogin" style="display: none"
		                                         onclick="location.href='/site/login?from=/promotion/p171213/'">未登录</span><span
				class="hasLogin" style="display: none"><span class="Num"><?= $netRechargeMoney ?></span>万元</span></p>
		<div class="btn-getReward clearfix">
			<div class="lf"></div>
			<div class="rg"></div>
		</div>
	</div>
	<div class="bottom-part">
		<p class="rules-title">活动规则</p>
		<a href="/user/userbank/refer" class="rules-link">网银充值教程>></a>
		<ul class="rules-list">
			<li>活动期间通过温都金服官网（www.wenjf.com）进行一次网银充值，即可领取“聪”礼包，内含<span>10元代金券、0.5%7天加息券、16积分；</span></li>
			<li>活动期间通过网银累计充值达到10万元，即可领取“慧”礼包，内含 <span>30元代金券、1.0%7天加息券、66积分；</span></li>
			<li>在网银充值中遇到任何问题，我们将为您提供全面的人工指导。
				<p>联系电话：400-101-5151；</p>
				<p>客服微信：wdjfNO1；</p>
				<p>门店地址：温州市鹿城区飞霞南路657号保</p>
				<p style="text-indent: 5em">丰大楼四层。</p>
			</li>
		</ul>
		<p class="rules-remind">本活动最终解释权归温都金服所有</p>
	</div>
</div>
<div class="mask" style="display: none"></div>
<div class="pop-reward " style="display: none">
	<img src="<?= FE_BASE_URI ?>wap/campaigns/internet-recharge/images/pic_reward_cong.png" alt="" class="pic-one"
	     style="display: none">
	<img src="<?= FE_BASE_URI ?>wap/campaigns/internet-recharge/images/pic_reward_hui.png" alt="" class="pic-two"
	     style="display: none;">
	<div class="pop-reward-button">收下奖品</div>
</div>
<div class="pop-fail" style="display: none">
	<div class="btn-close"></div>
	<p class="head-line">抱歉，您还没有领取资格哦，</p>
	<p class="head-line reason1" style="display: none">累计网银充值10万即可领取！</p>
	<p class="head-line reason2" style="display: none">成功使用网银充值即可领取！</p>
	<img src="<?= FE_BASE_URI ?>wap/campaigns/internet-recharge/images/pic_pop_fail.png" alt="">
	<p class="body-line">大额充值请登录温都金服官网，使用网银进行充值。充值遇到任何问题，欢迎您与我们联系，或者到线下门店获得指导。<a href="/user/userbank/refer" class="">网银充值教程>></a>
	</p>
	<div class="pop-fail-button">我知道了</div>
</div>
<div class="pop-has-hui" style="display: none">
	<div class="btn-close"></div>
	<p class="pop-has-title">您已经领取过该礼包了哦!</p>
	<div class="pop-fail-button">我知道了</div>
</div>
<div class="pop-has-cong" style="display: none">
	<div class="btn-close"></div>
	<p class="pop-has-title">您已经领取过该礼包了哦!</p>
	<div class="pop-fail-button">我知道了</div>
</div>

<script>
  $(function () {
    FastClick.attach(document.body);
    var promoStatus = $("input[name='promoStatus']").val();
    var isLoggedin = $("input[name='isLoggedin']").val();
    //初始化 登录与否
    if (isLoggedin == "false") {
      $(".recharge-number .hasLogin").hide().siblings().show();
    } else {
      $(".recharge-number .notLogin").hide().siblings().show();
    }

    $(".btn-getReward div").on("click", function () {
      if (promoStatus == 1) {
        Msg('活动未开始');
        return false;
      } else if (promoStatus == 2) {
        Msg("活动已结束");
        return false;
      } else {
        if (isLoggedin == "false") {
          location.href = '/site/login?from=/promotion/p171213';
        } else {
          var btnIndex = $(this).index();
          var xhr = $.get('/promotion/p171213/pull', {type: btnIndex + 1});
          xhr.done(function (res) {
            if (res.code == 0) {
              //可以领取
              if (btnIndex == 0) {
                $(".pop-reward .pic-one").show();
              } else {
                $(".pop-reward .pic-two").show();
              }
              $(".mask,.pop-reward").show();
              $("body").css("overflow", "hidden");
              $('body').on('touchmove', eventTarget, false);
              return false;
            } else if (res.code == 4) {
              //已领取，看列表
              if (btnIndex == 0) {
                $(".mask,.pop-has-cong").show();
              } else {
                $(".mask,.pop-has-hui").show();
              }
              $("body").css("overflow", "hidden");
              $('body').on('touchmove', eventTarget, false);
            }
          });
          xhr.fail(function (jqXHR) {
            if (400 === jqXHR.status && jqXHR.responseText) {
              var resp = jqXHR.responseJSON;
              if (resp.code == 3) {
                Msg('您还未登录');
                return false;
              } else if (resp.code == 1) {
                Msg('活动未开始');
                return false;
              } else if (resp.code == 2) {
                Msg("活动已结束");
                return false;
              } else if (resp.code == 7) {
                //不满足领取条件
                if (btnIndex == 0) {
                  $(".pop-fail .reason2").show();
                } else {
                  $(".pop-fail .reason1").show();
                }
                $(".mask,.pop-fail").show();
                $("body").css("overflow", "hidden");
                $('body').on('touchmove', eventTarget, false);
              }
            }
          })
        }
      }
    });

    function Msg(msg) {
      layer.open({
        content: msg
        , skin: 'msg'
        , time: 2 //2秒后自动关闭
      });
    }

    $(".pop-fail-button,.btn-close,.pop-reward-button").on("click", function () {
      $(this).parent().hide();
      $(".mask").hide();
      $(".pop-fail .reason1,.pop-fail .reason2").hide();
      $(".pop-reward img").hide();
      $("body").css("overflow", "auto");
      $('body').unbind('touchmove');
    });
    function eventTarget(event) {
      event.preventDefault();
    }
  })
</script>

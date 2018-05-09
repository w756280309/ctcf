<?php
$this->title = '优惠券';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>理财卡券</title>
	<link rel="stylesheet" href="http://view.wendujf.com/css/base.css?v=20170907">
	<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
	<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
	<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/account-coupon/index.css?v=1802131">
	<style>
		.list {
			-webkit-overflow-scrolling: touch;
		}

		* {
			-webkit-box-sizing: content-box;
			box-sizing: content-box;
		}
		.title-box * {
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
		}
		label {
			display: inline;
			font-weight: 400;
		}

		.coupon-box .warning {
			font-size: .34666667rem;
			height: .34666667rem;
			line-height: .34666667rem;
			color: rgb(244, 67, 54);
			text-align: center;
			margin-bottom: 0.3rem;
		}
	</style>
</head>
<body>
<div class="flex-content my-coupons">
    <div class="menu clearfix">
        <div class="blue-slide active"><span>代金券</span></div>
        <div class="red-slide"><span>加息券</span></div>
    </div>
    <div class="list-box">
			<!--代金券：-->
			<div class="list">
                <div class="btn-rules">
                    <a class="a-exchange">我有兑换码</a>
                    <a class="a-rule">查看规则</a>
                </div>
                <div class="no-content
                        <?php
                    if (count($model)) {
                        echo 'hide';
                    } else {
                        $num = 0;
                        foreach ($model as $v) {
                            if ($v['type'] == 0) $num++;
                            if ($num > 0) echo 'hide';
                        }
                    }
                    ?>">
                    <h4>暂无代金券</h4>
                    <p class="txt">参与平台活动和关注平台动态将有机会获得代金券哦！</p>
                    <p class="bottom-tips"><a href="" class="sign-in">签到</a>获取代金券</p>
                </div>
                <div class="detail-wrapper">
                    <div class="detail-main">
                        <ul>
                        <?php foreach ($model as $val) : ?>
                            <?php if ($val['type'] < 1) : ?>
                                                        <li class="box-shadow">
                                                            <a href="<?php if (!$val['isUsed'] && $val['expiryDate'] > date('Y-m-d')) {
                                  echo '/deal/deal/index';
                              } else {
                              } ?>" class="clearfix">
                                                                <div class="icon">
                                    <?php
                                    if ($val['isUsed'] == 1 || $val['expiryDate'] < date('Y-m-d')) {
                                        echo '<img src="' . FE_BASE_URI . 'wap/rate-coupon/account-coupon/images/grey-coupon-bg.png">';
                                    } else {
                                        echo '<img src="' . FE_BASE_URI . 'wap/rate-coupon/account-coupon/images/blue-coupon-bg.png">';
                                    }
                                    ?>
                                                                    <span class="num"><i>￥</i><?= $val['amount'] ?></span>
                                                                    <span class="text">满<i
                                                                            class="money"><?php echo $val['minInvest'] < 10000 ? intval($val['minInvest']) : intval($val['minInvest']) / 10000 . "万"; ?></i>元可用</span>
                                                                </div>
                                                                <div class="content">
                                                                    <h4 class="name"><?= $val['name'] ?></h4>
                                                                    <div class="extra">
                                                                <span class="unable" style="line-height: 1.3rem;">
                                                                    <?php
                                                                    if ($val['loanCategories'] == 1) {
                                                                        echo '楚盈金产品可用';
                                                                    } else if ($val['loanCategories'] == 2) {
                                                                        echo '楚盈恒产品可用';
                                                                    } else {
                                                                        echo '正式标产品可用';
                                                                    }
                                                                    ?>
                                                                </span>
                                                                    </div>
                                                                    <div class="bottom">
                                                                        <span class="time">有效期至<?= $val['expiryDate'] ?></span>
                                      <?php
                                      if (!$val['isUsed'] && $val['expiryDate'] >= date('Y-m-d')) {
                                          echo '<span class="go-to-use able-status">去使用</span>';
                                      } else if ($val['isUsed']) {
                                          echo '<span class="go-to-use">已使用</span>';
                                      } else {
                                          echo '<span class="go-to-use">已过期</span>';
                                      }
                                      ?>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                <div class="detail-bottom">
                    <div class="title-bottom"><span class="line"></span><i>温馨提示</i></div>
                    <p>代金券在出借项目时使用，使用后待项目收益开始时，将成为您总资产的一部分。</p>
                </div>
			</div>
			<!--加息券：-->
			<div class="list">
                <div class="btn-rules">
                    <a class="a-rule">查看规则</a>
                </div>
                <div class="no-content
                                    <?php
                    $num = 0;
                    foreach ($model as $v) {
                        if ($v['type'] == 1) $num++;
                        if ($num > 0) echo 'hide';
                    }
                    ?>" id="no-jxq" style="display: none">
                    <h4>暂无加息券</h4>
                    <p class="txt">参与平台活动和关注平台动态将有机会获得加息券哦！</p>
                    <p class="bottom-tips"><a href="" class="sign-in">签到</a>获取加息券</p>
                </div>
                <div class="detail-wrapper" id="detail-wrapper">
                    <div class="detail-main">
                        <ul>
                <?php foreach ($model as $val) : ?>
                    <?php if ($val['type'] == 1) : ?>
                                                <li class="box-shadow">
                                                    <a href="<?php if (!$val['isUsed'] && $val['expiryDate'] > date('Y-m-d')) {
                          echo '/deal/deal/index';
                      } else {
                          echo 'javascript:void(0)';
                      } ?>" class="clearfix">
                                                        <div class="icon">
                            <?php
                            if ($val['isUsed'] == 1 || $val['expiryDate'] < date('Y-m-d')) {
                                echo '<img src="' . FE_BASE_URI . 'wap/rate-coupon/account-coupon/images/grey-coupon-bg.png">';
                            } else {
                                echo '<img src="' . FE_BASE_URI . 'wap/rate-coupon/account-coupon/images/orange-coupon-bg.png">';
                            }
                            ?>
                                                    <span class="num"><i></i>+<?= round($val['bonusRate'], 1) ?>%</span>
                                                    <span class="text">加息<i
                                                            class="money"><?= $val['bonusDays'] ?></i>天</span>
                                                </div>
                                                <div class="content">
                                                    <h4 class="name"><?= $val['name'] ?></h4>
                                                    <div class="extra">
                                                    <span class="unable">
                                                    <?php
                                                    if ($val['loanCategories'] == 1) {
                                                        echo '楚盈金产品可用';
                                                    } else if ($val['loanCategories'] == 2) {
                                                        echo '楚盈恒产品可用';
                                                    } else if ($val['loanCategories'] == 3) {
                                                        echo '仅用于新手标产品';
                                                    } else {
                                                        echo '正式标产品可用';
                                                    }
                                                    ?>
                                                    </span>
                                                    <span class="unable">满<?php echo $val['minInvest'] < 10000 ? intval($val['minInvest']) : intval($val['minInvest']) / 10000 . "万"; ?>元可用</span>

                                                    </div>
                                                    <div class="bottom">
                                                        <span class="time">有效期至<?= $val['expiryDate'] ?></span>
                      <?php
                      if (!$val['isUsed'] && $val['expiryDate'] >= date('Y-m-d')) {
                          echo '<span class="go-to-use able-status">去使用</span>';
                      } else if ($val['isUsed']) {
                          echo '<span class="go-to-use">已使用</span>';
                      } else {
                          echo '<span class="go-to-use">已过期</span>';
                      }
                      ?>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="detail-bottom" id="detail-wrapper1">
                    <div class="title-bottom"><span class="line"></span><i>温馨提示</i></div>
                    <p>加息收益将随最后一期本息一起回到您的账户，项目中途转让将不享受任何加息券带来的收益。</p>
                </div>
			</div>
    </div>
	<div class="draw-coupon-box">
		<div class="coupon-pops">
			<img src="<?= FE_BASE_URI ?>wap/rate-coupon/account-coupon/images/draw-close.png" class="draw-close" alt="关闭按钮">
			<h4 style="color:#ff6707;">优惠券规则</h4>
			<ul>
				<li class="rule-title">规则概述</li>
				<li>理财卡券只适用于部分标的，其中转让标不可使用，同一项目代金券和加息券不可叠加使用。</li>
				<li class="rule-title">代金券</li>
				<li>1、一次出借可使用多张代金券，多张代金券使用需要满足出借额不少于多张代金券的累计金额。</li>
				<li>2、代金券不能与加息券叠加使用。
				<li>3、代金券使用后，所出借项目开始收益后，代金券金额将成为账户总资产的一部分。</li>
				<li class="rule-title">加息券
				<li>1、一次出借使用1张加息券，使用后项目在加息时间内获得额外年化收益。</li>
				<li>2、加息券不可叠加使用，也不与其他卡券叠加使用。</li>
				<li>3、通过加息券获得的收益将随项目最后一期本息一期回到您的账户；项目中途转让将不享受任何加息券带来的收益。</li>
				<li class="tel-bottom">详情咨询：<?= Yii::$app->params['platform_info.contact_tel'] ?></li>
			</ul>
		</div>
	</div>
	<input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken; ?>">
	<div class="coupon-box">
		<div class="coupon-pops">
			<img src="<?= FE_BASE_URI ?>wap/rate-coupon/account-coupon/images/exchange-close2.png" class="draw-close"
			     alt="关闭按钮">
			<div class="exchange-title">输入兑换码</div>
			<div class="exchange-input" style="padding: 0.9rem 0 0.55rem;">
				<label for="">兑换码</label>
				<input type="text" value="" placeholder="请输入卡券兑换码">
			</div>
			<p class="warning"></p>
			<a class="btn-exchange">立即兑换</a>
			<p class="tips">请在有效期内使用兑换码，过期将无法使用</p>
			<p class="tips tips-last">兑换码通过楚天财富宣传页、合作网站的活动所得</p>
		</div>
	</div>
</div>
<script>
  $(function () {
      $('.menu > div').on('click', function(){
          var index = $(this).index();
          $('.menu > div').removeClass('active');
          $('.menu > div').eq(index).addClass('active');
          $('.list-box .list').hide();
          $('.list-box .list').eq(index).show();
      });
    $('#no-jiaxiquan').on('click', function () {
      $('#no-jxq').show();
      $(this).hide();
      $('#detail-wrapper').hide();
      $('#detail-wrapper1').hide();
    });
    // 规则
    $('.btn-rules .a-rule').on('click', function () {
      $('.draw-coupon-box').show();
      $('.container').on('touchmove', eventTarget, false);
    });
    $('.draw-coupon-box .draw-close').on('click', function () {
      $('.draw-coupon-box').hide();
      $('.container').off('touchmove');
    });

    // 兑换
    $('.btn-rules .a-exchange').on('click', function () {
      $('.coupon-box').show();
      $('.container').on('touchmove', eventTarget, false);
    });
    $('.coupon-box .draw-close').on('click', function () {
      $('.coupon-box').hide();
      $('.container').off('touchmove');
    });
    $(".coupon-box .btn-exchange").on("click", function () {
      var code = $(".coupon-box .exchange-input input").val();
		var _csrf = $("input[name=_csrf]").val();
      if (code == '') {
        $(".warning").html("请输入兑换码");
        return false;
      }
      if (code.length !== 16) {
        $(".warning").html("兑换码有误，请重新输入");
        return false;
      }
      var reg = /^[a-zA-Z0-9]{16}$/;
      if (!reg.test(code)) {
        $(".warning").html("兑换码有误，请重新输入");
        return false;
      }
      $(".warning").html("");
      $.ajax({
        url: "/user/couponcode/duihuan",
        data: {
        	code:code,
			_csrf:_csrf
		},
        type: 'POST',
        dataType: 'json',
        success: function (data) {
          if (data.requireLogin === 1) {
            location.href = "/site/login";
          }
          if (data.code > 0) {
            if (data.code === 1) {
              $(".coupon-box .exchange-input input").val('');
            }
          } else {
            $(".warning").html("");
            $(".coupon-box .exchange-input input").val("");
            $("#success-refer").html(data.data);
            $(".coupon-box .btn-exchange").html("继续兑换");
          }
			$(".warning").html(data.message);
        }
      })
    });


    function eventTarget(event) {
      var e = event || window.event;
      e.preventDefault();
    }

  })

</script>
</body>
</html>

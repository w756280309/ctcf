<?php

$fromNb = \common\models\affiliation\Affiliator::isFromNb(Yii::$app->request);
if ($fromNb) {
    $this->title = '现代金报-楚天财富';
} else {
    $this->title = '楚天财富';
}

$this->params['breadcrumbs'][] = $this->title;
$this->hideHeaderNav = true;
$this->showBottomNav = true;

use yii\helpers\Html;
use yii\web\JqueryAsset;
use common\view\LoanHelper;
use common\utils\StringUtils;


$this->registerCssFile(FE_BASE_URI . "libs/swiper/swiper-3.4.2.min.css");
$this->registerCssFile(FE_BASE_URI . "wap/common/css/wenjfbase.css?v=171028");
$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/mask/mask.min.css?v=1.681');
$this->registerJsFile(FE_BASE_URI . 'libs/lib.flexible3.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI . 'res/js/js.cookie.js', ['depends' => JqueryAsset::class, 'position' => 1]);
$this->registerJsFile(FE_BASE_URI . 'libs/fastclick.js', ['depends' => JqueryAsset::class]);
$this->registerJsFile(FE_BASE_URI . 'libs/jquery.lazyload.min.js', ['depends' => JqueryAsset::class]);
$this->registerJsFile(FE_BASE_URI . 'libs/swiper/swiper-3.4.2.min.js', ['depends' => JqueryAsset::class]);
//$this->registerJsFile(ASSETS_BASE_URI . 'ctcf/js/mask/mask.js?v=1.11113', ['depends' => JqueryAsset::class]);
?>
<style>
	.pop {
		position: fixed;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		-moz-transform: translate(-50%, -50%);
		-webkit-transform: translate(-50%, -50%);
		z-index: 100000
	}

	.pop img:nth-of-type(1) {
		width: .76rem;
		position: absolute;
		top: -11.5%;
		right: 0
	}

	.pop img:nth-of-type(2) {
		width: 8rem;
		height: 10.66667rem
	}
	#btn_udesk_im{
		display: block;
		width: 49.7%;
		height: 2.586rem;
	}
	#btn_udesk_im img{
		width: 100%;
		height: 100%;
		margin:0;
	}
	.data-box-show .data-box-show-line3{
		line-height:0.6rem;
		height:0.6rem;
	}
	.data-box-show .data-box-show-line3 span{
		font-weight: 800;
	}
</style>
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/new-homepage/index.css?v=18021211121">
<!--<div>-->
<!--    <img class="channel hide" src="--><?//= FE_BASE_URI ?><!--wap/index/images/channel.png" width="100%" alt="">-->
<!--</div>-->
<div class="flex-content">
    <div class="mask" style="display: none"></div>
    <!--新增首页弹框1-->
    <div class="first-popover" style="display: none">
        <img class="popover-bg" src="<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/popover_01.png" alt="">
        <div class="popover-ctn">
            <img class="popover-close" src="<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/popover-close.png" alt="">
            <p class="popover-title">价值698元代金券<br>已发到您的账户</p>
            <p class="popover-detail">你也可以在账户-代金券中,查看奖励</p>
            <a class="btn_01" href="/user/coupon/list">查看详情</a>
            <a class="btn_02" href="javascript:void(0)">去出借 领取160元超市卡</a>
        </div>
    </div>

    <!--新增首页弹框2-->
    <div class="second-popover" style="display: none">
        <img class="popover-close" src="<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/popover-close_01.png" alt="">
        <p class="popover-title">出借前先开通资金托管账户</p>
        <p class="popover-detail">开通账户送积分，出借还有更多超市卡等你拿</p>
        <a class="popover-btn" href="/user/identity">马上去开通</a>
    </div>

    <!--已登陆未出借弹窗-->
    <div class="mask-no-invest">
        <div class="popup-box">
            <div class="popup-box-top"></div>
            <i class="close-box"></i>
            <p class="popup-box-msg"><span>888元</span>红包已经发放到账户中心，请至”账户中心-优惠券”中查看</p>
            <div class="red-racket">
                <span>8元</span>
                <span>20元</span>
                <span>30元</span>
                <span>80元</span>
                <span>150元</span>
                <span>220元</span>
                <span>380元</span>
            </div>
            <a href="/user/coupon/list/" class="popup-box-btn">查看红包</a>
        </div>
    </div>
    <!--未登录-->
    <div class="mask-prize-hint">
        <div class="popup-box">
            <div class="popup-box-top"></div>
            <i class="close-box"></i>
            <div class="prize_user_box">
                <a class="prize_user_new clearfix">
                    <img class="lf" src="<?= ASSETS_BASE_URI ?>ctcf/images/mask/prize_user_new.png" alt="">
                    <div class="rg">
                        <p class="user-login-msg">新用户登录</p>
                        <p class="user-login-prize">送您<span>888元</span>红包</p>
                    </div>
                </a>
                <a class="prize_user_old clearfix">
                    <img class="lf" src="<?= ASSETS_BASE_URI ?>ctcf/images/mask/prize_user_old1.png" alt="">
                    <div class="rg">
                        <p class="user-login-msg">老用户登录</p>
                        <p class="user-login-prize">送您升级红包</p>
                    </div>
                </a>
            </div>
            <a href="/user/coupon/list/" class="popup-box-btn">点击领取</a>
        </div>
    </div>
    <!--已登陆已出借-->
    <div class="mask-login-invest">
        <div class="popup-box">
            <i class="close-box"></i>
            <div class="urgrade-swiper-contain">
                <ul class="swiper-wrapper" id="upgrade-sweper">
                    <li class="swiper-slide upgrade_explain">
                        <h5>尊敬的用户，您好：</h5>
                        <p class="login-invest-msg">2018年楚天财富系统全新升级，并建立了全新会员和积分体系。平台根据您的历史出借额，特发放了豪华升级礼包，敬请收下！</p>
                        <table>
                            <tr>
                                <th>累计年化出借金额（万元）</th>
                                <th>升级红包</th>
                                <th>升级积分</th>
                            </tr>
                            <tr>
                                <td>0＜X＜20</td>
                                <td>118元</td>
                                <td>188</td>
                            </tr>
                            <tr>
                                <td>20≤X＜50</td>
                                <td>268元</td>
                                <td>388</td>
                            </tr>
                            <tr>
                                <td>≥50</td>
                                <td>480元</td>
                                <td>588</td>
                            </tr>
                        </table>
                        <a class="check-login-invest">查看我的升级红包</a>
                    </li>
                    <li class="swiper-slide upgrade-updata">
                        <h5>您获得的升级礼包</h5>
                        <div class="upgrade-updata-contain">
                            <div class="updata-top-part">

                            </div>
                            <div class="updata-mid-part">

                            </div>
                            <div class="updata-bottom-part clearfix">
                                <div class="lf bottom-jf-prize">
                                    <i></i>
                                </div>
                                <div class="rg updata-bottom-rg">

                                </div>
                            </div>
                        </div>
                        <u class="get-prize-rules">补偿规则</u>
                        <a href="/user/coupon/list/" class="check-get-prize">查看红包</a>
                    </li>
                </ul>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>

    <?php if (!empty($hotActs)) { ?>
			<div class="banner-box swiper-container">
				<div class="swiper-wrapper" id="index_banner">
            <?php
            $firstHotAdv = current($hotActs);
            if (isset($firstHotAdv) && !is_null($firstHotAdv->media)) {
                ?>
							<a href="<?= $firstHotAdv->getLinkUrl() ?>" class="swiper-slide"><img
									src="<?= UPLOAD_BASE_URI . $firstHotAdv->media->uri ?>" alt=""></a>
                <?php
            }
            ?>
				</div>
				<div class="swiper-pagination"></div>
			</div>
    <?php } ?>
	<div class="newbid-box" style="display:none">
		<div class="newbid-insideBox">
			<span class="newbid-insideBox-title">新手专享</span>
			<p class="newbid-insideBox-shouyiNum">10%</p>
			<p class="newbid-insideBox-shouyiTxt">预期年化收益</p>
			<img src="<?= ASSETS_BASE_URI ?>ctcf/images/shouye/pic_newbid_new.png" class="newbid-insideBox-pic">
			<a href="/site/signup" class="newbid-insideBox-register">注册</a>
			<a href="/site/login" class="newbid-insideBox-login">登录</a>
		</div>
		<p class="newbid-wendu-link"><a href="/site/h5?wx_share_key=h5" style="color: #538feb;">1分钟了解楚天财富</a></p>
		<p class="newbid-wendu-txt">湖北日报新媒体集团旗下平台</p>
	</div>
	<div class="links-box clearfix">
		<a href="/user/checkin" class="lf">
			<span class="links-box-iconJifen"></span>
			<img src="<?= ASSETS_BASE_URI ?>ctcf/images/shouye/icon_qd.png" alt=""
			     >
			<p class="links-box-txt">签到</p>
		</a>
		<a
			href="/site/app-download?redirect=/mall/portal/guest<?= (Yii::$app->request->get('token') && defined('IN_APP')) ? '?token=' . Yii::$app->request->get('token') : '' ?>"
			class="lf">
			<img src="<?= ASSETS_BASE_URI ?>ctcf/images/shouye/icon_jf.png" alt=""
			     >
			<p class="links-box-txt">积分商城</p>
		</a>
		<a href="/user/invite" class="lf">
			<img src="<?= ASSETS_BASE_URI ?>ctcf/images/shouye/icon_yq.png" alt=""
			     >
			<p class="links-box-txt">邀请好友</p>
		</a>
		<a href="/news/promo" class="lf">
			<img src="<?= ASSETS_BASE_URI ?>ctcf/images/shouye/icon_gg.png" alt=""
			     >
			<p class="links-box-txt">活动专区</p>
		</a>
	</div>
<!--    理财专区-->
    <?php if ($loans) { ?>
	<div class="bids-box">
        <h5 class="bids-box-title clearfix"><span class="lf">精选散标</span></h5>
        <ul class="bids-box-bidlist clearfix">
            <?php foreach ($loans as $val) { ?>
            <li>
                <a href="/deal/deal/detail?sn=<?= $val->sn ?>">
                    <div class="bid-title">
                        <h6 class="bid-title-h6"><?= $val->title ?></h6>
                        <?php if (!empty($val->tags) || $val->pointsMultiple > 1) : ?>
                                <?= $this->renderFile("@common/views/tags.php", ['loan' => $val]) ?>
                        <?php endif; ?>
                    </div>
                    <div class="bid-main-msg clearfix">
                        <div class="lf bid-about-rate">
                            <p class="bid-rate-number"><?= LoanHelper::getDealRate($val) ?><span>%</span><?php if (!empty($val->jiaxi)) { ?><i>+<?= doubleval($val->jiaxi) ?>%</i><?php } ?></p>
                            <p class="bid-rate-msg">借贷双方约定利率</p>
                        </div>
                        <div class="lf bid-about-data">
                            <span>期限&nbsp;&nbsp;<i><?php $ex = $val->getDuration() ?><?= $ex['value']?><?= $ex['unit']?></i></span>
                            <p><?= StringUtils::amountFormat2($val->start_money) ?>元起借</p>
                        </div>
                    </div>
                </a>
            </li>
            <?php } ?>
        </ul>
        <a href="/deal/deal/index" class="bids-box-more">查看更多</a>
	</div>
    <?php } ?>
    <?php if (!empty($news)) { ?>
<!--        平台动态-->
			<div class="reading-box">
				<p class="reading-box-title">平台动态</p>
				<ul class="reading-box-list">
            <?php foreach ($news as $key => $new) { ?>
							<li>
								<a href="/news/detail?id=<?= $new->id ?>&v=<?= time() ?>">
									<p class="reading-box-list-til"><?= $new->title ?></p>
									<p class="reading-box-list-des clearfix">
										<span class="lf">平台公告</span>
										<span class="rg"><?= date('Y-m-d', $new->news_time) ?></span>
									</p>
								</a>
							</li>
            <?php } ?>
				</ul>
				<a href="/news" class="reading-box-more">查看更多</a>
			</div>
    <?php } ?>
<!--    平台数据-->
	<div class="data-box">
		<p class="data-box-title">平台数据</p>
		<div class="data-box-show">
			<p class="data-box-show-line1">平台已安全运营</p>
			<p class="data-box-show-line2">
				<span><?= (new \DateTime(date('Y-m-d')))->diff(new DateTime('2015-06-05'))->days ?></span>天</p>
			<p class="data-box-show-line3" style="width: auto;display: inline-block;padding: 0 .306rem">历史兑付率<span>100%</span></p>
		</div>
	</div>
<!--    服务中心-->
	<div class="phone-box">
		<p class="phone-box-title">服务中心</p>
		<div class="phone-box-links clearfix">
			<p id="btn_udesk_im" class="lf">
                <a href="/site/udesk" style="width:100%">
                    <img src="<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/go_refer.png" alt="">
                </a>
            </p>
			<a href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>" class="rg"><img
					src="<?= ASSETS_BASE_URI ?>ctcf/images/new-homepage/go_phone_summer.png" alt=""></a>
		</div>
		<p class="phone-box-address">地址：武汉市武昌区东湖路181号楚天文化创意产业园<br/>8号楼1层</p>
	</div>
    <div class="aboutus-box swiper-container">
        <div class="swiper-wrapper">
            <a href="/site/h5?wx_share_key=h5" class="swiper-slide">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/shouye/with_us.png" alt="">
            </a>
            <a href="/safeguard/index" class="swiper-slide">
                <img src="<?= ASSETS_BASE_URI ?>ctcf/images/shouye/with_safegread_new.png" alt="" >
            </a>
        </div>
    </div>
</div>

<?php if (!defined('IN_APP') && $this->showBottomNav) { ?>
    <?= $this->renderFile('@wap/views/layouts/footer.php') ?>
<?php } ?>
<div class="mask" style="display: none"></div>
<!--新增首页弹框1-->
<!--<div class="first-popover" style="display: none">
	<img class="popover-bg" src="<?/*= ASSETS_BASE_URI */?>ctcf/images/new-homepage/popover_01.png" alt="">
	<div class="popover-ctn">
		<img class="popover-close" src="<?/*= ASSETS_BASE_URI */?>ctcf/images/new-homepage/popover-close.png" alt="">
		<p class="popover-title">价值698元代金券<br>已发到您的账户</p>
		<p class="popover-detail">你也可以在账户-代金券中,查看奖励</p>
		<a class="btn_01" href="/user/coupon/list">查看详情</a>
		<a class="btn_02" href="javascript:void(0)">去出借 领取160元超市卡</a>
	</div>
</div>-->

<!--新增首页弹框2-->
<!--<div class="second-popover" style="display: none">
	<img class="popover-close" src="<?/*= ASSETS_BASE_URI */?>ctcf/images/new-homepage/popover-close_01.png" alt="">
	<p class="popover-title">出借前先开通资金托管账户</p>
	<p class="popover-detail">开通账户送积分，出借还有更多超市卡等你拿</p>
	<a class="popover-btn" href="/user/identity">马上去开通</a>
</div>-->

<!--对应的弹框的js-->
<!--<script>
  $('.popover-close').on('click', function () {
    $('.first-popover,.second-popover').hide();
    $('.mask').hide();
  })
</script>-->
<?php if (null !== $kaiPing && $kaiPing->media) { ?>
	<div class="mask hide"></div>

	<div class="pop hide">
		<img src="<?= FE_BASE_URI ?>wap/index/images/close.png" class="close_splash" alt="">
		<img src="<?= UPLOAD_BASE_URI . $kaiPing->media->uri ?>" onclick="window.location.href='<?= $kaiPing->link ?>'"
		     alt="">
	</div>
<?php } ?>
<script>
  function closeAdv() {
    $('.mask').addClass('hide');
    $('.pop').addClass('hide');
    document.body.removeEventListener('touchmove', eventTarget, false);
  }

  function eventTarget(event) {
    event.preventDefault();
  }

  function checkStatus() {
    $.ajax({
      type: 'get',
      url: '/site/xs',
      success: function (code) {
        //会员是否是登录状态
        if (!code.isLoggedIn) {
          $('.newbid-box').css('display', 'block');
        }
        //判断是否是出借者
        if (code.isInvestor) {
          if (Cookies.get('showIndexPop')) {
            Cookies.remove('showIndexPop');
            $('.mask,.first-popover').show();
            $('.second-popover').hide();
          }
        }
        //判断个人出借总额大于五万时，前端页面显示总金额
        if (code.showplatformStats) {
          $(".totalTradeBox").css('display', 'inline-block');
        }
      }
    })
  }

  var guiZe = '<?= (empty($kaiPing) || empty($kaiPing->media)) ? '' : $kaiPing->media->uri ?>';
</script>
<script type="text/template" id="banner_text">
    <?php foreach ($hotActs as $act) {
        if (!is_null($act->media)) {
            ?>
					<a class="swiper-slide" href="<?= $act->getLinkUrl() ?>"><img src="<?= UPLOAD_BASE_URI . $act->media->uri ?>"
					                                                              alt=""> </a>
            <?php
        }
    }
    ?>
</script>
<?php
$this->registerJs(<<<JSFILE
    $(function (){
        addToken();
        FastClick.attach(document.body);
        
        $("img").lazyload({
            threshold: 200
        });
        $('.popover-ctn .btn_02').on('click', function(){
            $('.first-popover').hide();
            $('.second-popover,.mask').show();
        });
        //瑞安渠道
        var source = Cookies.get('campaign_source');
        if (source == 'rarb') {
            $('.channel').removeClass('hide');
        }
        //开屏图
        $('.close_splash').on('click', closeAdv);
        if (guiZe) {
            if (Cookies.get('splash_show') !== guiZe) {
                document.body.addEventListener('touchmove', eventTarget);
                Cookies.set('splash_show', guiZe);
                $('.mask').removeClass('hide');
                $('.pop').removeClass('hide');
                setTimeout(closeAdv, 6000);
            }
        }
        //统计数据
        $.get('/site/stats-for-index', function (data) {
            $('#totalTradeAmount').html(Math.floor(WDJF.numberFormat(accDiv(data.totalTradeAmount, 100000000), 0))+'亿\+');
            $('#totalRefundAmount').html(Math.floor(WDJF.numberFormat(accDiv(data.totalRefundAmount, 100000000), 0))+'亿\+');
            $('#totalRefundInterest').html(Math.floor(WDJF.numberFormat(accDiv(data.totalRefundInterest, 100000000), 0))+'亿\+');
        });
        //判断首页上方登录状态
        checkStatus();
        
        var host = location.host;
        if(host.split('.')[0] === 'app') {
            $('footer').css({'margin-bottom': 0});
        }
         //替换首页banner图
        $('#index_banner').html($('#banner_text').text());
        //轮播图
        var bannerSwiper = new Swiper('.banner-box', {
            autoplay: 3000,//可选选项，自动滑动
            speed: 500,
            loop:true,
            pagination : '.swiper-pagination',
            autoplayDisableOnInteraction: false
        });
        var aboutusSwiper = new Swiper('.aboutus-box', {
            autoplayDisableOnInteraction: false,
            slidesPerView: 2,
            spaceBetween:10
        });
        $.ajax({
            type: "GET",
            url: "oldUserRewardPop/pop-type",
            success: function(data){
                if (data == 0) {
    
                } else if (data == 1) {
                    removeMove();
                    $('.mask-prize-hint').css('display','block').find('i.close-box').on('click',function(){
                        $('.flex-content').off('touchmove');
                        $('.mask-prize-hint').css('display','none');
                    });
                } else if (data == 2) {
                    removeMove();
                    $('.mask-no-invest').css('display','block').find('i.close-box').on('click',function(){
                        $('.flex-content').off('touchmove');
                        $('.mask-no-invest').css('display','none');
                    });
                } else if (data == 3) {
                    $('.updata-top-part').html("<span>8元</span><span>30元</span><span>80元</span>");
                    $('.updata-mid-part').html('共得<span>118元</span>红包');
                    $('.updata-bottom-rg').html("<p>188积分</p><p>可以兑换超多礼品</p>");
                    removeMove();
                    $('.mask-login-invest').css('display','block').find('i.close-box').on('click',function(){
                        $('.flex-content').off('touchmove');
                        $('.mask-login-invest').css('display','none');
                    });
    
                } else if (data == 4) {
                    $('.updata-top-part').html("<span>8元</span><span>30元</span><span>80元</span><span>150元</span>");
                    $('.updata-mid-part').html('共得<span>268元</span>红包');
                    $('.updata-bottom-rg').html("<p>388积分</p><p>可以兑换超多礼品</p>");
                    removeMove();
                    $('.mask-login-invest').css('display','block').find('i.close-box').on('click',function(){
                        $('.flex-content').off('touchmove');
                        $('.mask-login-invest').css('display','none');
                    });
                } else if (data == 5) {
                    $('.updata-top-part').html("<span>30元</span><span>80元</span><span>150元</span><span>220元</span>");
                    $('.updata-mid-part').html('共得<span>480元</span>红包');
                    $('.updata-bottom-rg').html("<p>588积分</p><p>可以兑换超多礼品</p>");
                    removeMove();
                    $('.mask-login-invest').css('display','block').find('i.close-box').on('click',function(){
                        $('.flex-content').off('touchmove');
                        $('.mask-login-invest').css('display','none');
                    });
                }
                $.ajax({
                    type:'get',
                    url:'oldUserRewardPop/after-pop',
                });
            },
            error:function(){
    
            }
        });
        // 全局禁止滑动
        function removeMove(){
            $('.flex-content').on('touchmove',function(e){
                var e=e||window.event;
                e.preventDefault();
            });
        }
        var mySwiper;
        var timer = setTimeout(function(){
            // 已登陆注册弹框
            mySwiper = new Swiper('.urgrade-swiper-contain', {
                spaceBetween:100,
                initialSlide :0,
                speed: 500,
                loop:false,
                pagination:'.swiper-pagination',
                autoplayDisableOnInteraction: false,
                onSlideChangeEnd:function(){
                    if(mySwiper.activeIndex==0){
                        $('.mask-login-invest').find('.close-box').css('display','none');
                    }else if(mySwiper.activeIndex==1){
                        $('.mask-login-invest').find('.close-box').css('display','block');
                    }
                }
    
            });
            clearTimeout(timer);
        }, 200);
        // 点击查看升级红包显示下一个页面
        $('.check-login-invest').on('click',function(){
            mySwiper.slideTo(1, 500);
    
        });
        // 点击补偿规则返回上一个页面
        $('u.get-prize-rules').on('click',function(){
            mySwiper.slideTo(0, 500);
            // $('.mask-login-invest').find('.close-box').css('display','block');
        });
    })
JSFILE
) ?>

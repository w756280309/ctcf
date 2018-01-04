<?php

use common\models\adv\Share;

$this->title = '2017温都金服年报';
$hostInfo = Yii::$app->params['clientOption']['host']['wap'];
$this->share = new Share([
    'title' => '这是我的2017年报，快来看看吧！',
    'description' => '温都金服，市民身边的财富管家',
    'imgUrl' => 'https://static.wenjf.com/upload/link/link1515029207433498.png',
    'url' => $hostInfo.'/promotion/p2017/s1?sc='.$userCode,
]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/animate/animate.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>libs/swiper/swiper-3.4.2.min.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180102/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/vue.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/swiper/swiper.animate.min.js"></script>
<script src="<?= FE_BASE_URI ?>libs/swiper/swiper-3.4.2.min.js"></script>
<style type="text/css">
    [v-cloak] {
        display: none
    }
</style>
<div class="flex-content" id="app">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide page-one">
                <div class="content">
                    <img class="cloud1 ani" swiper-animate-effect="fadeInLeft" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/cloud_01.png" alt="">
                    <img class="flower1 ani" swiper-animate-effect="fadeInRight" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/flower_01.png" alt="">
                    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/logo.png" alt="">
                    <img class="title" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/title_01.png" alt="">
                    <dl class="clearfix">
                        <dt class="lf">
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h320"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h130"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h30"></span>
                        </dt>
                        <dd class="lf">
                            <img class="intro_01 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="1s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_01.png" alt="">
                            <p class="p1 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s" ><?= $platOnlineDateOut ?>温都平台正式上线</p>
                            <p class="p2 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s" >我们在第<?= $platToRegisterDays ?>天相遇了</p>
                            <p class="p3 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="1s" swiper-animate-delay="0.6s"><?= $registerDateOut ?></p>
                            <p class="p4 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="1s" swiper-animate-delay="0.6s">我来到了温都金服大家庭</p>
                            <p class="p5 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="1s" swiper-animate-delay="1s">未来，温都金服将继续与您携手同行</p>
                        </dd>
                    </dl>
                </div>
                <img class="btm-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/page_01.png" alt="">
            </div>

            <div class="swiper-slide page-two">
                <div class="content">
                    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/logo.png" alt="">
                    <img class="title" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/title_02.png" alt="">
                    <img class="cloud2 ani" swiper-animate-effect="fadeInLeft" swiper-animate-duration="0.5s" swiper-animate-delay="0s"  src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/cloud_02.png" alt="">
                    <img class="flower2 ani" swiper-animate-effect="fadeInRight" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/flower_02.png" alt="">
                    <p class="days"><?= $registerToTodayDays ?><span>天</span></p>
                    <img class="intro_02" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_02.png" alt="">
                    <dl class="clearfix">
                        <dt class="lf">
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h165"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h40"></span>
                        </dt>
                        <dd class="lf">
                            <p class="p1 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s">温都金服平台自上线以来</p>
                            <p class="p2 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s">已安全运营<?= $platSafeDays ?>天</p>
                            <p class="p3 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s">兑付率100%</p>
                            <p class="p4 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s">未来，温都金服将继续与您携手同行</p>
                        </dd>
                    </dl>
                </div>
                <img class="btm-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/page_02.png" alt="">
            </div>

            <div class="swiper-slide page_three">
                <div class="content">
                    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/logo.png" alt="">
                    <img class="cloud1 ani" swiper-animate-effect="fadeInLeft" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/cloud_01.png" alt="">
                    <img class="flower1 ani" swiper-animate-effect="fadeInRight" swiper-animate-duration="0.5s" swiper-animate-delay="0s"  src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/flower_01.png" alt="">
                    <dl class="clearfix">
                        <dt class="lf">
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h345"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h270"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h40"></span>
                        </dt>
                        <dd class="lf">
                            <div class="status-one" v-if="!isStart">
                                <p class="ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.2s">虽然我在2017年没有理财</p>
                                <p class="ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.2s">但这一天不会遥远</p>
                                <img class="ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_04.png" alt="">
                            </div>
                            <div class="status-two" v-if="isStart">
                                <p class="ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.2s"><?= $investDateOut ?></p>
                                <p class="ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.2s">我开始了第一次理财</p>
                                <img class="ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_03.png" alt="">
                            </div>
                            <p class="p1 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s">温都金服平台交易额一直稳步攀升</p>
                            <p class="p2 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s">截至目前：</p>
                            <p class="p3 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.6s">已累计兑付<?= $platRefundAmount ?>亿元</p>
                            <p class="p4 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.6s">为用户赚取收益<?= $platRefundInterest ?>亿元人民币</p>
                            <p class="p5 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="1s">未来，温都金服将继续与您携手同行</p>
                        </dd>
                    </dl>
                </div>
                <img class="btm-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/page_03.png" alt="">
            </div>

            <div class="swiper-slide page-four">
                <div class="content">
                    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/logo.png" alt="">
                    <img class="cloud2 ani" swiper-animate-effect="fadeInLeft" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/cloud_02.png" alt="">
                    <img class="flower2 ani" swiper-animate-effect="fadeInRight" swiper-animate-duration="0.5s" swiper-animate-delay="0s"  src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/flower_02.png" alt="">
                    <dl class="clearfix">
                        <dt class="lf">
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h495"></span>
                        </dt>
                        <dd class="lf">
                            <p class="p1 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s">一年多以来，我获得了</p>
                            <p class="p2 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s"><?= $totalProfit ?><span>元收益</span></p>
                            <p class="p3 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s">还额外获得了</p>
                            <ul class="clearfix">
                                <li style="position: relative;border: 1px solid #c3554d;border-radius: 50%;" class="lf mr70  ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s">
                                    <span><?= $totalPoints ?></span><br>积分
                                    <canvas id="myCanvas0" style="width: 2.08rem;height: 2.08rem;position: absolute;top:-0.025rem;left: -0.025rem;z-index: -1;padding: 0.133333rem;border-radius: 50%;"></canvas>
                                </li>
                                <li style="position: relative;border: 1px solid #c3554d;border-radius: 50%;" class="lf ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s">
                                    <span><?= $totalRedPacket ?>元</span><br>现金红包
                                    <canvas id="myCanvas1" style="width: 2.08rem;height: 2.08rem;position: absolute;top:-0.025rem;left: -0.025rem;z-index: -1;border-radius: 50%;padding: 0.133333rem;"></canvas>
                                </li>
                                <li style="position: relative;border: 1px solid #c3554d;border-radius: 50%;" class="lf mr70 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s">
                                    <span><?= $couponNum ?>张</span><br>代金券
                                    <canvas id="myCanvas2" style="width: 2.08rem;height: 2.08rem;position: absolute;top:-0.025rem;left: -0.025rem;z-index: -1;border-radius: 50%;padding: 0.133333rem;"></canvas>
                                </li>
                                <li style="position: relative;border: 1px solid #c3554d;border-radius: 50%;" class="lf ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.4s">
                                    <span><?= $bonusCouponNum ?>张</span><br>加息券
                                    <canvas id="myCanvas3" style="width: 2.08rem;height: 2.08rem;position: absolute;top:-0.025rem;left: -0.025rem;z-index: -1;border-radius: 50%;padding: 0.133333rem;"></canvas>
                                </li>
                            </ul>
                        </dd>
                    </dl>
                    <div class="intro ani" swiper-animate-effect="fadeIn" swiper-animate-duration="0.8s" swiper-animate-delay="1.2s">击败了平台<?= $totalProfitRanking ?>%的用户</div>
                </div>
                <img class="btm-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/page_04.png" alt="">
            </div>

            <div class="swiper-slide page-five">
                <div class="content">
                    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/logo.png" alt="">
                    <img class="title" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/title_03.png" alt="">
                    <img class="cloud2 ani" swiper-animate-effect="fadeInLeft" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/cloud_02.png" alt="">
                    <img class="flower3 ani" swiper-animate-effect="fadeInRight" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/flower_03.png" alt="">
                    <div class="status_one" v-if="isMiss">
                        <p>2017.5.20周年庆活动当天</p>
                        <p>我错过了5倍积分和520元现金红包</p>
                        <p>好遗憾...</p>
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_05.png" alt="">
                    </div>
                    <div class="status_two" v-if="!isMiss">
                        <p>2017.5.20周年庆活动当天</p>
                        <p>我抓住了机会，领到了</p>
                        <p><?= $investPointsIn520 ?>额外积分和<?= $redPacketIn520 ?>元现金红包</p>
                        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_06.png" alt="">
                    </div>
                    <dl class="clearfix">
                        <dt class="lf">
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h145"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h40"></span>
                        </dt>
                        <dd class="lf">
                            <p class="p1 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s">周年庆活动获得了广大客户的信赖</p>
                            <p class="p2 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s">与支持</p>
                            <p class="p3 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.2s">单日交易额突破6924万元</p>
                            <p class="p4 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.6s">未来，温都金服将继续与您携手同行</p>
                        </dd>
                    </dl>
                </div>
                <img class="btm-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/page_05.png" alt="">
            </div>

            <div class="swiper-slide page-six">
                <div class="content">
                    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/logo.png" alt="">
                    <img class="title" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/title_04.png" alt="">
                    <img class="intro" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_07.png" alt="">
                    <img class="cloud2 ani" swiper-animate-effect="fadeInLeft" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/cloud_02.png" alt="">
                    <img class="flower3 ani" swiper-animate-effect="fadeInRight" swiper-animate-duration="0.5s" swiper-animate-delay="0s"  src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/flower_03.png" alt="">
                    <dl class="clearfix">
                        <dt class="lf">
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h145"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h380"></span>
                        </dt>
                        <dd class="lf">
                            <p class="p1 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0s">自2017年5月温都金服成立“520慈善基金”以来，我已经累计捐献了<span><?= $charityAmount ?></span>元</p>
                            <img class="ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="1s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_08.png" alt="">
                            <p class="p2 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.6s">2017.5.20，温都金服520慈善基金正式启动。从当天起，温都金服平台上销售的每一笔“慈善专属产品”，都将按照一定比例捐赠给温州市慈善总会温州都市报分会。</p>
                        </dd>
                    </dl>
                </div>
                <img class="btm-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/page_06.png" alt="">
            </div>

            <div class="swiper-slide page-seven">
                <div class="content">
                    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/logo.png" alt="">
                    <img class="cloud2 ani" swiper-animate-effect="fadeInLeft" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/cloud_02.png" alt="">
                    <img class="flower2 ani" swiper-animate-effect="fadeInRight" swiper-animate-duration="0.5s" swiper-animate-delay="0s"  src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/flower_02.png" alt="">
                    <dl class="clearfix">
                        <dt class="lf">
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h225"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h95"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h100"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                            <span class="line h120"></span>
                            <img class="point" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/point.png" alt="">
                        </dt>
                        <dd class="lf">
                            <p class="p1 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.2s">2017年1月，获得ICP证书</p>
                            <img class="icp ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="1s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/icp.png" alt="">
                            <p class="p2 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="0.6s">2017年11月，首批接入国家级合同保全的金融平台</p>
                            <p class="p3 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="1s">2017年12月，喜获中信国安战略投资</p>
                            <p class="p4 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="1.4s">我们将继续努力奉行“稳健 专业 贴心”的价值观与“安全理财，幸福千万家”的使命</p>
                            <p class="p5 ani" swiper-animate-effect="fadeInDown" swiper-animate-duration="0.8s" swiper-animate-delay="1.8s">未来，温都金服将继续与您携手同行</p>
                        </dd>
                    </dl>
                </div>
                <img class="btm-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/page_07.png" alt="">
            </div>

            <div class="swiper-slide page-eight">
                <div class="content">
                    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/logo.png" alt="">
                    <img class="title" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/title_05.png" alt="">
                    <img class="intro" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/intro_09.png" alt="">
                    <img class="cloud2 ani" swiper-animate-effect="fadeInLeft" swiper-animate-duration="0.5s" swiper-animate-delay="0s" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/cloud_02.png" alt="">
                    <img class="flower4 ani" swiper-animate-effect="fadeInRight" swiper-animate-duration="0.5s" swiper-animate-delay="0s"  src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/flower_04.png" alt="">
                    <div class="extol">
                        <p>让我们携手同行</p>
                        <p>谱写安全合规新篇章</p>
                        <p>温都金服，有你更温暖</p>
                    </div>
                    <div class="share-page share">分享给好友</div>
                </div>
                <img class="btm-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180102/images/page_08.png" alt="">
            </div>
        </div>
    </div>
</div>


<script>
    var app = new Vue({
        el: '#app',
        data:{
            isStart:<?= $userIsInvested ?>,
            isMiss:<?= $showStaticIn520 ?>,
        }
    });

    var mySwiper = new Swiper ('.swiper-container', {
        direction : 'vertical',
        height: window.innerHeight,
        mousewheelControl : true,
        initialSlide: 0,
        onInit: function(swiper){
            swiperAnimateCache(swiper);
            swiperAnimate(swiper);
        },
        onSlideChangeEnd: function(swiper){
            swiperAnimate(swiper);
        },
        onTouchEnd: function (swiper) {
            swiperAnimate(swiper);
        }
    })



</script>
<script type="text/javascript">

    function drawCanvas(myCanvas){
        var canvas=document.getElementById(myCanvas);
        var cans=canvas.getContext('2d');

        //初始角度为0
        var step = 0;
        function loop(){
            //清空canvas
            cans.clearRect(0,0,canvas.width,canvas.height);
            //绘制矩形
            cans.fillStyle='#c3554d';

            //角度增加一度
            step-=3;
            //角度转换成弧度
            var angle = step*Math.PI/180;
            //矩形高度的变化量
            var deltaHeight = Math.sin(angle) * 4;
            //矩形高度的变化量(右上顶点)
            var deltaHeightRight = Math.cos(angle) * 4;
            cans.beginPath();
            //在矩形的左上与右上两个顶点加上高度变化量
            cans.moveTo(0, canvas.height/4+deltaHeight);
            //画曲线
            cans.bezierCurveTo(canvas.width /4, canvas.height/4+deltaHeight-2, canvas.width / 4,    canvas.height/4+deltaHeightRight-2, canvas.width, canvas.height/4+deltaHeightRight);
            cans.lineTo(canvas.width, canvas.height/2+deltaHeight+deltaHeightRight);
            cans.lineTo(canvas.width, canvas.height);
            cans.lineTo(0, canvas.height);
            cans.lineTo(0, canvas.height/2+deltaHeight);
            cans.fill();
            cans.closePath();


            window.requestAnimFrame = (function(){
                return window.requestAnimationFrame ||
                    window.webkitRequestAnimationFrame ||
                    window.mozRequestAnimationFrame ||
                    function( callback ){
                        window.setTimeout(callback, 1000 / 60);
                    };
            })();
            requestAnimFrame(loop);
        }
        loop();
    }
    drawCanvas("myCanvas0");
    drawCanvas("myCanvas1");
    drawCanvas("myCanvas2");
    drawCanvas("myCanvas3");
</script>

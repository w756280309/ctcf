<?php
use common\models\adv\Share;
$this->title = '七夕闯关大作战';
$this->share = new Share([
    'title' => '我在这里玩答题闯关获得了大红包！快来一起玩吧！',
    'description' => '楚天财富七夕献礼，海量红包、礼品送不停！',
    'imgUrl' => FE_BASE_URI.'wap/campaigns/active20170823/images/wx_share.png',
    'url' => Yii::$app->request->hostInfo.'/promotion/fest-77-in/index',
]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170823/css/invest.css">
<script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
<script src="<?= FE_BASE_URI ?>/libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>/libs/jquery-1.11.1.min.js"></script>
<div class="flex-content">
    <div class="part-one">
        <!--未登录显示去登录-->
        <?php if (is_null($sum)) { ?>
        <a href="/site/login?next=<?= urlencode(Yii::$app->request->absoluteUrl) ?>" class="go-login">点击登录>></a>
        <?php } else { ?>
        <!--登录后显示这行-->
        <div class="total">已累计年化<span><span id="number"><?= rtrim(rtrim(bcdiv($sum, 10000, 2), '0'), '.') ?></span>万元</span></div>
        <?php } ?>
        <div class="rules">查看规则 >></div>
    </div>
    <div class="part-two"></div>
    <div class="part-three"></div>
    <div class="part-four">

        <?php
            if ($status['code'] == 1) {
                echo '<a href="/deal/deal/index" class="go-invest"></a>';
                echo '<a href="third" class="next-level"></a>';
            } else {
                echo '<a href="javascript:;" class="go-invest end"></a>';
                echo '<a href="javascript:;" class="next-level end"></a>';
            }
        ?>

    </div>
    <div class="mask" style="display: none"></div>
    <div class="rule-box" style="display: none;">
        <h5>活动规则</h5>
        <img class="close-btn" src="<?= FE_BASE_URI ?>wap/campaigns/active20170823/images/close.png" alt="">
        <ol>
            <li>活动时间2017年8月28日-8月31日，以出借成功时间为准；</li>
            <li>本次活动面向所有楚天财富注册用户；</li>
            <li>活动期间出借楚天财富平台产品累计年化金额达到指定额度，即可获得相应礼品（不含转让产品）；
                <div class="reward-list">
                    <p class="leiji">累计年化金额（元）</p>
                    <p class="lipin">礼品</p>
                    <p class="jifen">对应积分</p>
                    <ul>
                        <li class="clearfix">
                            <p class="lj">5,200,000</p>
                            <p class="lp">周生生黄金手链</p>
                            <p class="jf">77777</p>
                        </li>
                        <li class="clearfix">
                            <p class="lj">1,880,000</p>
                            <p class="lp">周生生足金耳钉</p>
                            <p class="jf">17777</p>
                        </li>
                        <li class="clearfix">
                            <p class="lj">600,000</p>
                            <p class="lp">七夕化妆镜台灯</p>
                            <p class="jf">5777</p>
                        </li>
                        <li class="clearfix">
                            <p class="lj">100,000</p>
                            <p class="lp">车载情侣猪摆件</p>
                            <p class="jf">777</p>
                        </li>
                    </ul>
                </div>
            </li>
            <li>本次活动礼品将在活动结束后7个工作日内以积分形式发放，用户可以进入积分商城进行兑换。</li>
        </ol>
        <p class="regular-tips">注：年化出借金额=出借金额*项目期限/365</p>
        <p class="regular-tips">本次活动最终解释权归楚天财富所有</p>
    </div>
</div>
<script>
    $(function () {
        $(".rules").click(function () {
            $(".mask ,.rule-box").show();
            $('body').on('touchmove',eventTarget, false);
        });
        $(".close-btn").click(function () {
            $(".mask ,.rule-box").hide();
            $('body').off('touchmove');
        });

        function eventTarget(event) {
            var event = event || window.event;
            event.preventDefault();
        }
    })
    $('.end').on('click',function(){
        toastCenter('活动已结束');
    })
</script>
</body>
</html>
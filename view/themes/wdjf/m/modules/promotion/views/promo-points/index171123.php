<?php
$this->title = '感恩节回馈';
?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20171123/css/index.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script>
    $(function(){
        $(".mid-box-nav div a").click(function(){
            if($(".mid-box-nav div a")[0]==this){
                $(".mid-box-nav div a").eq(0).addClass("active");
                $(".mid-box-nav div a").eq(1).removeClass("active");
                $('.mid-box1-contain').fadeIn(200);
                $('.mid-box2-contain').fadeOut(200);
            }else if($(".mid-box-nav div a")[1]==this){
                $(".mid-box-nav div a").eq(1).addClass("active");
                $(".mid-box-nav div a").eq(0).removeClass("active");
                $('.mid-box1-contain').fadeOut(200);
                $('.mid-box2-contain').fadeIn(200);
            }
        })
        var promoStatus = $('input[name=promoStatus]').val();
        $("header a").click(function(){
            if(promoStatus==0){
                location.href='/deal/deal/index?t=1';
            } else if (promoStatus == 1) {
                toastCenter('活动未开始');
            } else if (promoStatus == 2) {
                toastCenter('活动已结束');
            }
        })

        //活动状态的弹框
        function toastCenter(val, active) {
            var $alert = $('<div class="error-info" style="display: block; position: fixed;font-size: .4rem;"><div>' + val + '</div></div>');
            $('body').append($alert);
            $alert.find('div').width($alert.width());
            setTimeout(function () {
                $alert.fadeOut();
                setTimeout(function () {
                    $alert.remove();
                }, 200);
                if (active) {
                    active();
                }
            }, 2000);
        }

    })
</script>
<div class="contain">
    <header>
        <p>已累计年化:<span><?= $totalMoney ?></span>万元</p>
        <a href="javascript:void(0)">去投资</a>
    </header>
    <div class="mid_divnav">
        <div class="mid_divnav_mid"></div>
    </div>
    <footer>
        <div class="mid-box">
            <div class="mid-box-nav">
                <div><a class="active">慢享轻奢</a></div>
                <div><a>超市卡</a></div>
            </div>
            <div class="mid-box1-contain clearfix">
                <a class="mid-box-contain-especial" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1480627">
                    <div class="especial-left-div">
                        <i></i>
                    </div>
                    <div class="especial-right-div">
                        <span>258888积分</span>
                        <span>Apple iphone X64G</span>
                        <u>立即兑换</u>
                    </div>
                </a>
                <a class="mid-box-contain-common" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1076295">
                    <div class="common-top">
                        <i></i>
                    </div>
                    <div class="common-bottom">
                        <span>97760积分</span>
                        <span>华为手机P10plus</span>
                        <u>立即兑换</u>
                    </div>
                </a>
                <a class="mid-box-contain-common" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1268343">
                    <div class="common-top">
                        <i></i>
                    </div>
                    <div class="common-bottom">
                        <span>36980积分</span>
                        <span>小米扫地机器人</span>
                        <u>立即兑换</u>
                    </div>
                </a>
                <a class="mid-box-contain-common" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=855863">
                    <div class="common-top">
                        <i></i>
                    </div>
                    <div class="common-bottom">
                        <span>25760积分</span>
                        <span>Luna mini2电子美容仪</span>
                        <u>立即兑换</u>
                    </div>
                </a>
                <a class="mid-box-contain-common" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=855866">
                    <div class="common-top">
                        <i></i>
                    </div>
                    <div class="common-bottom">
                        <span>13000积分</span>
                        <span>香奈儿淡香水50ml</span>
                        <u>立即兑换</u>
                    </div>
                </a>

            </div>
            <div class="mid-box2-contain clearfix">
                <a class="mid-box-contain-especial" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1333647">
                    <div class="especial-left-div">
                        <i></i>
                    </div>
                    <div class="especial-right-div">
                        <span>2000积分</span>
                        <span>100元人本超市卡</span>
                        <u>立即兑换</u>
                    </div>
                </a>
                <a class="mid-box-contain-common" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1333659">
                    <div class="common-top">
                        <i></i>
                    </div>
                    <div class="common-bottom" href="">
                        <span>1400积分</span>
                        <span>50元人本超市卡</span>
                        <u>立即兑换</u>
                    </div>
                </a>
                <a class="mid-box-contain-common" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=981034">
                    <div class="common-top">
                        <i></i>
                    </div>
                    <div class="common-bottom">
                        <span>1400积分</span>
                        <span>50元沃尔玛超市卡</span>
                        <u>立即兑换</u>
                    </div>
                </a>
                <a class="mid-box-contain-common" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1108956">
                    <div class="common-top">
                        <i></i>
                    </div>
                    <div class="common-bottom">
                        <span>1400积分</span>
                        <span>50元浙北超市卡</span>
                        <u>立即兑换</u>
                    </div>
                </a>
                <a class="mid-box-contain-common" href="/site/app-download?redirect=/mall/portal/guest?dbredirect=https://goods.m.duiba.com.cn/mobile/appItemDetail?appItemId=1108961">
                    <div class="common-top">
                        <i></i>
                    </div>
                    <div class="common-bottom">
                        <span>1400积分</span>
                        <span>50元大润发超市卡</span>
                        <u>立即兑换</u>
                    </div>
                </a>

            </div>
        </div>
        <div class="footer_bottom"></div>
    </footer>
</div>
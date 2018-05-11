<?php
$this->title = '母亲节送好礼';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/css/active-20180513/index.css?v=1.621">
<script src="<?= FE_BASE_URI ?>libs/fastclick.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="flex-content">
    <div class="active-banner"></div>
    <div class="prize-lise">
        <div class="prize-top clearfix">
            <div class="prize-top1 prize-box lf">
                <div class="over-prize get-prize1">
                    <i></i>
                </div>
            </div>
            <div class="prize-top2 prize-box lf">
                <div class="over-prize get-prize2">
                    <i></i>
                </div>
            </div>
            <div class="prize-top3 prize-box rg">
                <div class="over-prize get-prize3">
                    <i></i>
                </div>
            </div>
        </div>
        <div class="prize-bottom clearfix">
            <div class="prize-bottom1 prize-box lf">
                <div class="over-prize get-prize4">
                    <i></i>
                </div>
            </div>
            <div class="prize-bottom2 prize-box lf">
                <div class="over-prize get-prize5">
                    <i></i>
                </div>
            </div>
            <div class="prize-bottom3 prize-box rg">
                <div class="over-prize get-prize6">
                    <i></i>
                </div>
            </div>
        </div>
    </div>
    <a class="go-licai">去理财</a>
    <p class="all-invest">已累计年化：<span><?= $annualInvestAmount ?></span>万元</p>
    <div class="active-rule">
        <div class="rule-msg1">
            <i></i>
            <ul>
                <li>活动时间：2018年5月13日-2018年5月20日；</li>
                <li>本次活动面向所有楚天财富注册用户，PC端可同步参与；</li>
                <li>活动期间任意投资1次，即可获赠66积分；</li>
                <li>活动期间累计年化投资(不含转让产品)达到指定额度，即可获得相应礼品。</li>
            </ul>
        </div>
        <div class="rule-msg2">
            <u></u>
            <ul>
                <li class="clearfix">
                    <span class="lf">累计年化金额(元)</span>
                    <span class="rg">礼品</span>
                </li>
                <li class="clearfix">
                    <span class="lf">任意投资1次</span>
                    <span class="rg">66积分</span>
                </li class="clearfix">
                <li class="clearfix">
                    <span class="lf">10,000</span>
                    <span class="rg">6.6元现金红包</span>
                </li>
                <li class="clearfix">
                    <span class="lf">50,000</span>
                    <span class="rg">50元代金券</span>
                </li>
                <li class="clearfix">
                    <span class="lf">100,000</span>
                    <span class="rg">50元超市卡</span>
                </li>
                <li class="clearfix">
                    <span class="lf">200,000</span>
                    <span class="rg">100元超市卡</span>
                </li>
                <li class="clearfix">
                    <span class="lf">500,000</span>
                    <span class="rg">欧姆龙电子血压计</span>
                </li>
            </ul>
        </div>
        <p class="rule-msg3"><u></u>例：用户A在活动期间累计年化投资达到50万元，即可获得全部礼品。<br/>本活动虚拟奖品将立即发放到账，现金红包及实物奖品将于活动结束后7个工作日内联系发放，请保持通讯畅通。</p>
        <p class="last-p-msg">本活动与苹果公司无关<br/>本活动最终解释权归楚天财富所有</p>
    </div>
</div>

<script>
    window.onload=function(){
        // var dataJson={isLoggedIn:true,promoStatus:1,'180513_C50':true};
        FastClick.attach(document.body);
        var isLogin=dataJson.isLoggedIn;
        var active=dataJson.promoStatus;
        var prize1=document.querySelector(".get-prize1");
        var prize2=document.querySelector(".get-prize2");
        var prize3=document.querySelector(".get-prize3");
        var prize4=document.querySelector(".get-prize4");
        var prize5=document.querySelector(".get-prize5");
        var prize6=document.querySelector(".get-prize6");

        (dataJson['rewards']['180513_P66'])&&(prize1.style.display='block');
        (dataJson['rewards']['180513_C66'])&&(prize2.style.display='block');
        (dataJson['rewards']['180513_C50'])&&(prize3.style.display='block');
        (dataJson['rewards']['180513_G50'])&&(prize4.style.display='block');
        (dataJson['rewards']['180513_G100'])&&(prize5.style.display='block');
        (dataJson['rewards']['180513_XYJ'])&&(prize6.style.display='block');

        var goLicai=document.querySelector(".go-licai");
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
        };
        goLicai.onclick=function(){
            if(active===1){
                toastCenter("活动未开始");
            }else if(active===2){
                toastCenter("活动已结束");
            }else if(active===0){
                if(isLogin==false){
                    location.href="/site/login";
                }else{
                    location.href="/deal/deal/index";
                }
            }
        };
    }
</script>



<?php
$this->title = '圣诞狂欢派对 岁末感恩钜惠';
?>
<link rel="stylesheet" type="text/css" href="<?= FE_BASE_URI ?>wap/campaigns/active20181225/css/main.css">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/css/index.min.css?v=1.4">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/css/window-box.min.css?v=1.3">
<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>ctcf/promotion/active20180618/css/window-box.min.css?v=1.3">
<div class="main-container">
    <div class="top-banner">
        <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-top.jpg">
    </div>
    <div class="gift-box bg01">
        <dd class="btns-list">
            <a href="#rules2"><i class="icon icon01"></i>活动规则</a>
            <a href="#" id="share"><i class="icon icon02"></i>获得更多机会</a>
            <a href="#" id="get_gifts"><i class="icon icon03"></i>我的礼物</a>

        </dd>
        <div class="tit">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-tit01.png">
            <p>赢iPhone xs max</p>
        </div>
        <p class="rules">活动期间，用户通过分享活动或出借或邀请好友可获得拆圣诞礼物的次数，将有机会赢取<span style="color: #ffe59f;">iPhone xs max</span>。</p>
        <div class="gift-content">
            <?php if ($awardNums['draws'] > 0) : ?>
            <a href="" class="mainbtn gift-btn" id="open_gift">赶紧拆礼物</a>
            <?php endif; ?>
            <div class="gift-list">
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <dd><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-gift01.png"></dd>
                            <dd>Iphone Xs Max <br />256G</dd>
                        </td>
                        <td>
                            <dd><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-gift02.png"></dd>
                            <dd>戴森V10 Fluffy<br />吸尘器</dd>
                        </td>
                        <td>
                            <dd><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-gift03.png"></dd>
                            <dd>小米手环3</dd>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <dd><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-gift04.png"></dd>
                            <dd>20元代金券</dd>
                        </td>
                        <td>
                            <dd><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-gift05.png"></dd>
                            <dd>10元现金</dd>
                        </td>
                        <td>
                            <dd><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-gift06.png"></dd>
                            <dd>20积分</dd>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="point-box bg02">
        <div class="tit">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-tit02.png">
            <p>年终限时折扣秒杀</p>
        </div>
        <p class="rules">活动期间，每天上午10点，积分商城限时折扣兑换，数量有限，先到先兑，兑完即止。</p>
        <div class="point-content">
            <div class="point-part">
                <ul class="point-list">
                    <li><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-points01.png"></li>
                    <li><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-points02.png"></li>
                </ul>
                <a href="#" class="mainbtn point-btn">10积分限时秒杀</a>
            </div>
            <div class="point-part">
                <ul class="point-list">
                    <li><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-points03.png"></li>
                    <li><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-points04.png"></li>
                </ul>
                <a href="#" class="mainbtn point-btn">8折限时兑换</a>
            </div>
        </div>
    </div>
    <div class="card-box bg01">
        <div class="tit">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-tit03.png">
            <p>最高享300元京东卡</p>
        </div>
        <p class="rules">活动期间，老用户（非首次出借）累计出借金额（仅限半年标）达到相应金额可获得以下奖励：</p>
        <div class="card-list"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-card.png"></div>
        <a href="/deal/deal/index" class="mainbtn card-btn">立即出借</a>
    </div>
    <div class="rules-box bg01">
        <div class="tit">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-tit04.png">
        </div>
        <div class="rules-list">
            <p id="rules2">1、活动时间：2018年12月17日0：00 - 2018年12月28日23:59</p>
            <p>2、拆礼物规则：活动期间，微信分享活动、单笔出借满5000元、邀请好友注册并出借均可获得1次拆礼物的机会</p>
            <p>3、积分、代金券奖励将实时发放至平台账户内；现金奖励于活动结束后3个工作内发放至平台账户内</p>
            <p>4、超市卡、金龙鱼油、京东e卡奖励请于活动结束后10个工作日内前来公司前台领取</p>
            <p>5、如有恶意刷奖行为，一经查实，所得奖励将不予承兑</p>
            <p>6、本活动最终解释权归楚天财富所有，如有疑问，请咨询400-660-2799</p>
        </div>
        <a class="tips-arrow">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/icon-arrow.png">
        </a>
    </div>
</div>
<!--tanchuang-->
<div class="alert-container" style="display: none;">
    <div class="alert-content rules-content">
        <h2>活动规则</h2>
        <div class="rules-text">
            <p>1、用户通过微信分享活动页面至朋友圈，可拆礼物1次</p>
            <p>2、用户单笔出借金额满5000元，可拆礼物1次，金额越大，获得大奖的机率越大</p>
            <p>3、用户每成功邀请1位好友注册并出借，可拆礼物1次</p>
        </div>
    </div>
</div>
<!--tanchuang-->
<div class="alert-container" style="display: none;">
    <div class="alert-content chances-content">
        <ul class="chances-list">
            <li>
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-rule01.png">
                <span>分享至朋友圈<br />获得1次</span>
            </li>
            <li>
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-rule02.png">
                <span>单笔出借≥5千<br />获得1次</span>
            </li>
            <li>
                <img src="<?= FE_BASE_URI ?>wap/campaigns/active20181225/images/img-rule03.png">
                <span>邀请好友出借<br />获得1次</span>
            </li>
        </ul>
    </div>
</div>
<!--tanchuang-->
<div class="alert-container" style="display: none;">
    <div class="alert-content mygift-content">
        <h2>我的礼物</h2>
        <ul class="mygift-list">
            <li>20元代金券</li>
            <li>10元现金</li>
            <li>10积分</li>
        </ul>
    </div>
</div>
<!--share-box-->
<div class="mark-box"></div>
<div class="share-box">
    <img src="<?= FE_BASE_URI ?>images/invite/share.png" alt="">
</div>
</body>
<script type="text/javascript">
    $(function() {
        $("html").css("font-size", (window.innerWidth) / 3.75 + 'px');

        $('#share').on('click',function(){
            //判断域名,用以区别是否是app内嵌网页
            var protocol = window.location.protocol;
            var host = window.location.host.toLocaleLowerCase();
            if(host.substr(0,4)==='app.') {
                //分享四要素(标题+描述+链接地址+图标地址)
                var title = '我在楚天财富投资啦，也送你888元福利，拿去花';
                var des = '楚天财富隶属湖北日报新媒体集团旗下理财平台，平台稳健运行3年，投资更放心';
                var linkurl = invite_url;
                var thumurl = protocol+'//'+host+'/ctcf/images/promo/share_weixin1.jpg';
                var shareObj = {title : title, des:des,linkurl : linkurl, thumurl : thumurl};
                if(browser.versions.ios || browser.versions.iPad || browser.versions.iPhone) {
                    //苹果设备
                    window.webkit.messageHandlers.share.postMessage(shareObj);
                } else if(browser.versions.android) {
                    //android 设备,四个参数位置不可颠倒
                    window.shareAction.share(title,des,linkurl,thumurl);
                } else {
                    //其它
                    $('.mark-box').show();
                    $('.share-box').show();
                }
            } else {
                $('.mark-box').show();
                $('.share-box').show();
            }
        });
        $('.share-box').on('click',function(){
            $('.mark-box').hide();
            $('.share-box').hide();
        });
    });
    $("#open_gift").click(function(){
        $.get('/promotion/p181225/open-gift',[], function(data) {
            if(data['code'] !== 200){
                alert(data['message']);
            }else{
                alert(data['message']+',恭喜你获得'+data['data']['rewardName']+'!');
            }
        });
    });


</script>
</html>

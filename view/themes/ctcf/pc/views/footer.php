<?php

use frontend\assets\FrontAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/footer.css?v=20170519', ['depends' => FrontAsset::class]);
$this->registerJsFile(ASSETS_BASE_URI.'js/clipboard.min.js', ['depends' => FrontAsset::class]);

?>

<div class="footer-section footer-section5 footer-fp-auto-height footer-fp-section footer-fp-table">
    <div class="footer-five-box" style="height: 200px">
        <div class="footer-five-address">公司地址：湖北省武汉市东湖路181号楚天文化创意产业园8号楼1层</div>
        <div class="footer-five-tel">客服电话：<span><?= Yii::$app->params['platform_info.contact_tel'] ?></span>客服时间：<?= Yii::$app->params['platform_info.customer_service_time'] ?>（周一至周日）</div>
        <div class="footer-five-partner footer-clearfix"><i>合作伙伴：</i>
            <ul>
                <li style="border-left: 0;padding-left: 0;">荆楚网</li>
                <li>渤海银行</li>
                <li>众环海华</li>
                <li>湖北首义律师事务所</li>
                <li>楚天小贷</li>
                <li>长江小贷</li>
                <li style="border-left: 0;">信邦小贷</li>
                <li>网贷能量</li>
                <li>网贷之家</li>
            </ul>
        </div>
        <div class="footer-five-copyright">Copyright ©2014 www.hbctcf.com All Rights Reserved 鄂ICP备15002057号</div>
        <div class="footer-first-ma">
            <img src="<?= ASSETS_BASE_URI ?>images/ctcf/ma-new.png" alt="微信二维码">  <!-- TODO -->
            <div>访问手机wap版</div>
        </div>
    </div>
</div>

<!--关于我们-->
<div class="about-box">
    <div class="about-right">
        <a href="javascript:;" class="about-img1"></a>
        <a href="javascript:;" class="about-img2">
            <div class="about-left">
                <div class="about-img">
                    <p>官方微信</p>
                    <img src="<?= ASSETS_BASE_URI ?>images/ctcf/weixin.jpg" alt="">
                </div>
            </div>
        </a>
        <a href="javascript:;" class="about-img4 message">
            <span class="about-line message1">客服电话：<?= Yii::$app->params['platform_info.contact_tel'] ?></span>
        </a>
    </div>
</div>

<script>
    $(function() {
        $('.message').hover(function() {
            var index=$('.message').index(this);
            $('.message span').eq(index).stop(true,false).animate({right:'0px',opacity: 1},600);
        }, function() {
            $('.message1').animate({right:'-300px',opacity: 0},600);
        });

        if ($(window).scrollTop() <= 500) {
            $(".about-img1").hide();
        }

        $(window).scroll(function () {
            if ($(this).scrollTop() >= 500) {
                $(".about-img1").show();
            } else {
                $(".about-img1").hide();
            }
        });

        $('.about-img1').on('click', function() {
            $("html").animate({scrollTop:0});
            $("body").animate({scrollTop:0});
        });

        $('.about-img2').hover(function() {
            $('.about-left').stop(true,false).animate({right:0,opacity:1},600);
        }, function() {
            $('.about-left').animate({right:'-200px',opacity:0},600);
        });

        $('.about-img5').hover(function() {
            $('.about-app').stop(true,false).animate({right:0,opacity:1},600);
        }, function() {
            $('.about-app').animate({right:'-200px',opacity:0},600);
        });

        //复制
        if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE8.0" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE7.0" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE6.0" ){
            $('#copy-buttons').on('click',function(){
                alert('请手动复制QQ号');
            })
        } else {
            try {
                var btn = document.getElementById('copy-buttons');
                var clipboard = new Clipboard(btn);
                clipboard.on('success', function(e) {
                    alert('内容已复制到剪贴板');
                });

                clipboard.on('error', function(e) {
                    alert('请重新复制');
                });
            } catch(error) {

            }
        }
    })
</script>
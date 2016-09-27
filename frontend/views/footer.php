<?php
$this->registerCssFile(ASSETS_BASE_URI.'css/footer.css', ['depends' => 'frontend\assets\FrontAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/clipboard.min.js', ['depends' => 'frontend\assets\FrontAsset']);
?>

<div class="footer-section footer-section5 footer-fp-auto-height footer-fp-section footer-fp-table">
    <div class="footer-five-box" style="height: 200px">
        <div class="footer-five-address">公司地址：温州市鹿城区飞霞南路657号保丰大楼四层</div>
        <div class="footer-five-tel">客服电话：<span><?= Yii::$app->params['contact_tel'] ?></span><span style="padding-left: 8px;margin-right: 8px;">客服QQ：1430843929</span>客服时间：8:30-20:00（周一至周日）</div>
        <div class="footer-five-partner footer-clearfix"><i>合作伙伴：</i>
            <ul>
                <li style="border-left: 0;padding-left: 0;">温州日报</li>
                <li>温州晚报</li>
                <li>温州都市报</li>
                <li>温州商报</li>
                <li>科技金融时报</li>
                <li>温州网</li>
                <li>温州人杂志</li>
                <li style="border-left:0;padding-left: 0;">南京金融资产交易中心</li>
                <li>东方财富证券</li>
                <li>联动优势</li>
                <li>电子数据保全中心</li>
            </ul>
        </div>
        <div class="footer-five-copyright">Copyright ©温州温都金融信息服务股份有限公司 浙ICP备16003187号-1 浙公网安备 33030202000311号</div>
        <div class="footer-first-ma">
            <img src="<?= ASSETS_BASE_URI ?>images/ma.png" alt="">
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
                    <img src="<?= ASSETS_BASE_URI ?>images/weixin.png" alt="">
                </div>
            </div>
        </a>
        <a href="javascript:;" class="about-img3 message">
            <span id="copy-buttons"  data-clipboard-text="1430843929" class="about-qq message1">客服QQ：1430843929（点击复制）</span>
        </a>
        <a href="javascript:;" class="about-img4 message">
            <span class="about-line message1">客服电话：400-101-5151</span>
        </a>
        <a href="javascript:;" class="about-img5">
            <div class="about-app">
                <div class="aboutApp-img">
                    <p>扫一扫<br/>下载温都金服APP</p>
                    <img src="<?= ASSETS_BASE_URI ?>images/ma-app.png" alt="">
                </div>
            </div>
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

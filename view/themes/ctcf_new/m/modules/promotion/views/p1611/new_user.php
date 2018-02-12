<?php
$this->title = '你来就有蛋糕券';
$this->share = $share;
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>promo/1611/newuser/css/index.css">
<script src="<?= ASSETS_BASE_URI ?>js/fastclick.js"></script>
<script src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/js/flex.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
        var flag1 = 0;
        var flag2 = 0;
        $('.expand1 a').on('click', function() {
            $(this).parent().next().toggle();
            flag1++;
            $(this).find('.tip').css({'transform':'rotate('+180*flag1+'deg)'});
        })
        $('.expand2 a').on('click',function() {
            $(this).parent().next().toggle();
            flag2++;
            $(this).find('.tip').css({'transform':'rotate('+180*flag2+'deg)'});
        })
        $('.point').on('click',function() {
            if($('.expand1').next().is(":hidden")) {
                flag1=1;
                $('.expand1').next().show();
                $('.expand1').find('.tip').css({'transform':'rotate(180deg)'});
            }
        });
        var host = location.host;
        if(host.split('.')[0] === 'app') {
            $('#register').attr({'href' : '/site/signup'});
        }
    })
</script>

<div class="banner">
    <img src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/new_banner.jpg" alt="">
</div>
<div class="active">
    <img src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/active.png" alt="">
</div>
<div class="content">
    <div class="top">
        <p>注册送新手红包</p>
        <p><span>288</span>元代金券</p>
    </div>
    <div class="regular">
        <div class="list">
            <ul>
                <li class="clearfix"><span class="lf">8元代金券</span><img src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/line.png" alt=""><span class="rg">投资<i>1千元</i>可用</span></li>
                <li class="clearfix"><span class="lf">20元代金券</span><img src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/line1.png" alt=""><span class="rg">投资<i>1万元</i>可用</span></li>
                <li class="clearfix"><span class="lf">30元代金券</span><img src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/line2.png" alt=""><span class="rg">投资<i>2万元</i>可用</span></li>
                <li class="clearfix"><span class="lf">80元代金券</span><img src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/line3.png" alt=""><span class="rg">投资<i>10万元</i>可用</span></li>
                <li class="clearfix"><span class="lf">150元代金券</span><img src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/line4.png" alt=""><span class="rg">投资<i>20万元</i>可用</span></li>
            </ul>
        </div>
        <div class="btm">
            <img class="list-img" src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/regular.png" alt="">
        </div>
        <div class="construct">
            成功投资后，您还可以参与以下活动 <br>
            <img class="point" src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/circle.png" alt="">
        </div>
    </div>
    <div class="backactive">
        <div class="active1 clearfix expand1">
            <img class="gift lf" src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/gift1.png" alt="">
            <a><span class="lf">温都金服客户回馈活动</span><span class="rg">点击查看<img class="tip" src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/tip.jpg" alt=""></span></a>
        </div>
        <div class="show-hide">
            <ul>
                <li><span>●</span> 11月18日观影活动</li>
                <li><span>●</span> 11月19日星巴克咖啡教室</li>
                <li><span>●</span> 11月23日-11月27日会展中心寻宝游戏</li>
                <li><span>●</span> 12月3日户外一日游</li>
                <li><span>●</span> 12月10日亲子活动</li>
                <li><span>●</span> 12月19日-12月25日圣诞活动等</li>
            </ul>
            <p class="special">投资者如需参加，请拨打客服热线：<a href="tel:<?= Yii::$app->params['platform_info.contact_tel'] ?>"><?= Yii::$app->params['platform_info.contact_tel'] ?></a></p>
        </div>
    </div>
    <div class="backactive">
        <div class="active1 clearfix expand2">
            <img class="gift lf" src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/gift2.png" alt="">
            <a><span class="lf">百万年终奖回馈活动</span><span class="rg">点击查看<img class="tip" src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/tip.jpg" alt=""></span></a>
        </div>
        <div class="show-hide">
            <img class="listImg" src="<?= ASSETS_BASE_URI ?>promo/1611/newuser/images/list.png" alt="">
            <ul class="list-btm">
                <li><span>●</span> 累计投资金额为在2016/1/1 – 2016/12/31 期间购买160天及以上项目的累计投资金额（购买的转让项目不参加）</li>
                <li><span>●</span> 领奖时间为活动结束后7个工作日内</li>
            </ul>
        </div>
    </div>
    <div class="footer">
        <ul>
            <li>公司地址：温州市鹿城区飞霞南路657号保丰大楼四楼</li>
            <li>客服热线：<a href="tel:400-101-5151">400-101-5151</a> 或 <a href="tel:0577-55599998">0577-55599998</a></li>
            <li>官方网址：www.wenjf.com</li>
            <li>本活动最终解释权在法律范围内归温都金服所有</li>
            <li>理财非存款 产品有风险 投资须谨慎</li>
        </ul>
    </div>
</div>
<div class="nav">
    <ul>
        <li><a id="register" href="/luodiye/signup">马上注册</a></li>
        <li><a href="/deal/deal/index">立即投资</a></li>
        <li><a style="border:none;" href="/site/h5?wx_share_key=h5">了解温都金服</a></li>
    </ul>
</div>
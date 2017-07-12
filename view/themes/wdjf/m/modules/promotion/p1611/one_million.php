<?php
$this->title = '百万年终奖';
$this->share = $share;
?>

<link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>promo/1611/css/index.css?v=20161206">
<script src="<?= ASSETS_BASE_URI ?>promo/1611/js/lib.flexible2.js"></script>
<script>
    $(function() {
        var host = location.host;
        if(host.split('.')[0] === 'app') {
            $('#register').attr({'href':'/site/signup'});
        }
    })
</script>

<div class="banner"></div>
<div class="container">
    <div class="top">
        <img src="<?= ASSETS_BASE_URI ?>promo/1611/images/wave.png" alt="">
        <h3>活动详情</h3>
        <img src="<?= ASSETS_BASE_URI ?>promo/1611/images/wave.png" alt="">
    </div>
    <div class="content">
        <div class="gift1">
            <p>一重礼：注册送新手红包288元代金券</p>
            <ul>
                <li><em>8元</em>代金券<span><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span>投资1000元可用</li>
                <li><em>20元</em>代金券<span class="line02"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span>投资10000元可用</li>
                <li><em>30元</em>代金券<span class="line03"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span>投资20000元可用</li>
                <li><em>80元</em>代金券<span class="line04"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span>投资100000元可用</li>
                <li><em>150元</em>代金券<span class="line05"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span>投资200000元可用</li>
              </ul>
        </div>
        <div class="gift2">
            <p>二重礼：温都金服客户回馈活动</p>
            <ul>
                <li><img  src="<?= ASSETS_BASE_URI ?>promo/1611/images/dot.png" alt="">11月18日观影活动</li>
                <li><img  src="<?= ASSETS_BASE_URI ?>promo/1611/images/dot.png" alt="">11月19日星巴克咖啡教室</li>
                <li><img  src="<?= ASSETS_BASE_URI ?>promo/1611/images/dot.png" alt="">11月23日-11月27日会展中心寻宝游戏</li>
                <li><img  src="<?= ASSETS_BASE_URI ?>promo/1611/images/dot.png" alt="">12月3日户外一日游</li>
                <li><img  src="<?= ASSETS_BASE_URI ?>promo/1611/images/dot.png" alt="">12月10日亲子活动</li>
                <li><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dot.png" alt="">12月19日-12月25日圣诞活动等</li>
                <li class="special">（注：投资者如需参加，请通过客服热线报名）</li>
            </ul>
        </div>
        <div class="gift3">
            <p>三重礼： 年终累计投资回馈</p>
            <ul>
                <li class="clearfix"><i id="lfl">累计投资金额</i><span class="line00"></span><i id="lft">回馈礼品</i></li>
                <li>1000万<span class="line01"><img  src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span><i><em>豪华</em>旅游券</i></li>
                <li>500万<span class="line02"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span><i><em>超值</em>旅游券</i></li>
                <li>300万<span class="line03"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span><i><em>3000元</em>超市卡</i></li>
                <li>100万<span class="line04"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span><i><em>1000元</em>超市卡</i></li>
                <li>50万<span class="line05"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span><i><em>300元</em>超市卡</i></li>
                <li>10万<span class="line06"><img src="<?= ASSETS_BASE_URI ?>promo/1611/images/dotted.png" alt=""></span><i><em>精美</em>礼品</i></li>
                <li class="special">（注：2016.1.1-2016.12.31期间购买<em>160天及以上</em>项目的累计投资金额【 <em>购买的转让项目不参与</em>】奖品领取时间为活动结束后7个工作日内）
                </li>
            </ul>
        </div>
    </div>
    <div class="footer">
        <ul>
            <li>公司地址：温州市鹿城区飞霞南路657号保丰大楼四楼</li>
            <li>客服电话：<a href="tel:400-101-5151" class="special">400-101-5151</a></li>
            <li>官方网址：www.wenjf.com</li>
            <li>本活动最终解释权在法律范围内归温都金服所有</li>
            <li class="special">理财非存款 产品有风险 投资须谨慎</li>
        </ul>
    </div>
    <div class="nav">
        <ul>
            <li><a id="register" href="/luodiye/signup">马上注册</a></li>
            <li><a href="/deal/deal/index">立即投资</a></li>
            <li><a style="border:none;" href="/site/h5?wx_share_key=h5">了解温都金服</a></li>
        </ul>
    </div>
</div>

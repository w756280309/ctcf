<?php

$this->title = '小微贷项目推荐';
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20180619/css/index.min.css?v=1.0">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>

<div class="flex-content">
    <img onclick="return false;" class="banner" src="<?= FE_BASE_URI ?>wap/campaigns/active20180619/images/banner.jpg" alt="">
    <div class="part-one">
        <div class="bg"></div>
        <div class="content">
            <img onclick="return false;" class="intro-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20180619/images/intro.png" alt="">
            <div class="detail">
                <p class="title"><i class="red">* </i>计算公式举例<i class="red"> *</i></p>
                <ul>
                    <li><i class="red f30">原：</i>小明购买了10万元到期本息项目，期限36个月，约定利率9.2%，最终能拿到本金+约定收益共计12万7600元。</li>
                    <li><img onclick="return false;" src="<?= FE_BASE_URI ?>wap/campaigns/active20180619/images/angle.png" alt=""></li>
                    <li><i class="red f30">现：</i>小明购买了10万元等额本息项目，期限36个月，约定利率9.2%，每个月拿到的回款（部分本金+部分约定收益）去复投其他项目，继续产生收益，最终能拿到约定收益+复投收益！</li>
                    <li><img onclick="return false;" src="<?= FE_BASE_URI ?>wap/campaigns/active20180619/images/angle.png" alt=""></li>
                    <li class="red">选择等额本息进行复投<br>最终的预期收益将高于到期本息！</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="part-two">
        <div class="bg"></div>
        <div class="content">
            <img onclick="return false;" class="icon" src="<?= FE_BASE_URI ?>wap/campaigns/active20180619/images/icon.png" alt="">
            <div class="invest">
                <img onclick="return false;" src="<?= FE_BASE_URI ?>wap/campaigns/active20180619/images/invest.png" alt="">
                <a id="list24" class="m24" href="javascript:void(0);">查看详情</a>
                <a id="list36" class="m36" href="javascript:void(0);">查看详情</a>
            </div>
            <img onclick="return false;" class="intro" src="<?= FE_BASE_URI ?>wap/campaigns/active20180619/images/intro_01.png" alt="">
        </div>
    </div>
</div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="<?= FE_BASE_URI ?>libs/wxShare.js"></script>
<script>
    document.onreadystatechange = function () {
        if (document.readyState === "complete") {
            document.body.style.opacity = 1;
        }
    };
    window.onload = function(){
        var list24 = document.getElementById("list24"),list36 = document.getElementById("list36");
        if(dataJson.isLoggedIn){
            list24.setAttribute('href','/promotion/p180620/list?bidTime=24');
            list36.setAttribute('href','/promotion/p180620/list?bidTime=36');
        } else {
            list24.setAttribute('href','/site/login');
            list36.setAttribute('href','/site/login');
        }
        wxShare.setParams("温都金服小微贷项目——等额本息全新还款计划上线！", "点击链接，了解详情~", "<?= Yii::$app->params['clientOption']['host']['wap'] ?>promotion/p180620/index", "https://static.wenjf.com/upload/link/link1529465517828870.jpg", "<?= Yii::$app->params['weixin']['appId'] ?>", "/promotion/p180620/index/add-share");
    }
</script>
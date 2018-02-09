<?php

use common\models\adv\Share;

$this->title = '温都金服月老祠';
$this->share = new Share([
    'title' => '我抽取了一根月老签，你也来和我一起测缘分，好不好？',
    'description' => '在温都金服月老祠，抽中专属于你的那枚签',
    'imgUrl' => FE_BASE_URI.'wap/campaigns/active20170821/images/wx_share.jpg',
    'url' => Yii::$app->request->hostInfo.'/promotion/fest77/',
]);
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=1.0">
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/campaigns/active20170821/css/index.css?v=1.2">
<script src="<?= FE_BASE_URI ?>wap/common/js/com.js"></script>
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<script src="<?= FE_BASE_URI ?>libs/jquery-1.11.1.min.js"></script>
<div class="mask" style="display: none">请在微信端<br>打开链接</div>
<div class="flex-content">
    <img class="logo" src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/logo.png" alt="">
    <div class="ctn-bg">
        <div class="bg-one">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/bg_01.png" alt="">
        </div>
        <div class="bg-two">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/bg_02.png" alt="">
        </div>
        <div class="bg-three">
            <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/bg_03.png" style="height: 4rem;" alt="">
            <img class="lover" src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/lover.png" alt="">
            <div class="svg-box">
                <svg xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink"
                     width="412.618px" height="274.618px">
                    <path fill-rule="evenodd" stroke="rgb(255, 0, 0)" stroke-width="3.745px" stroke-linecap="butt"
                          stroke-linejoin="miter" fill="none"
                          d="M5.370,196.179 C5.370,196.179 75.191,269.551 199.791,184.024 C199.791,184.024 270.980,143.998 287.888,122.236 C287.888,122.236 369.046,47.419 278.774,10.814 C278.774,10.814 233.986,-4.955 204.854,71.589 C204.854,71.589 180.118,-23.511 109.669,14.866 C109.669,14.866 29.116,57.023 131.946,140.468 C131.946,140.468 206.160,202.022 408.388,267.084 "/>
                </svg>
            </div>
            <p class="tips"><img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/text.png" alt=""></p>
        </div>
        <div class="poetry">
            <ul class="clearfix">
                <li class="lf ">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/yue.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/xia.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/lao.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/ren.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/qian_01.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/hong.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/xian.png" alt="">
                </li>
                <li class="rg">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/yi.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/gen.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/yan.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/yuan.png" alt="">
                    <img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/qian.png" alt="">
                </li>
            </ul>
        </div>

        <div class="yaoqian animated bounceInDown">
            <img class="gif-img" src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/yaoqian.png" alt="">
            <div class="rocker"></div>
        </div>
    </div>
</div>
<a id="anchor" href="#Myanchor"></a>
<div id="Myanchor"></div>
<script>
    $(function () {
        $('.poetry ul li.rg img,.poetry ul li.lf img,.tips').css({opacity: 1});
        $('.poetry').css({opacity: 0});
        $('.rocker').on('click', rocker);
        function rocker() {
            $(this).off('click');
            $('.gif-img').attr({src:'<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/yaoqian.gif'});
            setTimeout(function () {
                $('.flex-content').fadeOut(1000,function(){
                    location.replace('/promotion/fest77/result?xcode='+Math.ceil(Math.random()*49));
                })
            },1000)
        }
        var isWX = moduleFn.parseUA();
        if(!isWX.weixin){
            $('.mask').show();
        }
        $('#anchor')[0].click();
    })
</script>
<img src="<?= FE_BASE_URI ?>wap/campaigns/active20170821/images/yaoqian.gif" style="width: 0;height: 0;" alt="">
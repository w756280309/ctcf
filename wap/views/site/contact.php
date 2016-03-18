<?php
$this->title = '联系我们';
?>
<style>
    body {
        background: #fff!important;
        font-size: 62.5%;
    }
    .relation {
        padding-bottom: 25px;
    }
    .relation .nav-height {
        margin-bottom: 0!important;
    }
    .relation .ico {
        padding: 0;
        margin:13px 16px;
    }
    .relation .ico img {
        width:100%;
    }
    .relation .about-content {
        margin:0 auto;
        padding-left: 17px;
        padding-right: 17px;
    }
    .relation .about-content div.xinxi {
        padding-top:10px;
        clear: both;
    }
    .relation .about-content div.mendian {
        padding-top:30px;
        clear: both;
    }
    .relation .about-content div p {
        font-size: 1rem;
        line-height: 1.6rem;
    }
    .relation .about-content .p_float {
        float: left;
        width:21%;
        height: 100%;
    }
    .relation .about-content .p_num {
        float: left;
        width:79%;
        height: 100%;
    }

    /*iphone6*/
    @media screen and (min-width: 375px) and (max-width: 434px) {
        .relation .about-content div p {
            padding-left: 1%;
            font-size: 1.5rem;
            line-height: 2.6rem;
        }
        .relation .about-content .p_float {
            width:21%;
        }
        .relation .about-content .p_num {
            width:77%;
        }
    }
    /*  媒体查询  */
    @media screen and (min-width: 435px)and (max-width: 480px) {
        .relation .about-content div p {
            padding-left: 1%;
            font-size: 1.5rem;
            line-height: 2.5rem;
        }
        .relation .about-content .p_float {
            width:19%;
        }
        .relation .about-content .p_num {
            width:79%;
        }
    }
    @media screen and (min-width: 481px)and (max-width: 567px) {
        .relation .about-content div p {
            padding-left: 1%;
            font-size: 1.6rem;
            line-height: 2.8rem;
        }
        .relation .about-content .p_float {
            width:16%;
        }
        .relation .about-content .p_num {
            width:81%;
        }
    }
    @media screen and (min-width: 568px) and (max-width: 666px) {
        .relation .about-content div p {
            padding-left: 1%;
            font-size: 1.8rem;
            line-height: 3.2rem;
        }
        .relation .about-content .p_float {
            width:18%;
        }
        .relation .about-content .p_num {
            width:80%;
        }
    }
    @media screen and (min-width: 667px) and (max-width: 767px) {
        .relation .about-content div p {
            padding-left: 1%;
            font-size: 1.9rem;
            line-height: 3.4rem;
        }
        .relation .about-content .p_float {
            width:16%;
        }
        .relation .about-content .p_num {
            width:82%;
        }
    }
    @media screen and (min-width: 768px) and (max-width: 800px){
        .relation .about-content div p {
            padding-left:1% ;
            font-size: 2rem;
            line-height: 3.8rem;
        }
        .relation .about-content .p_float {
            width:14%;
        }
        .relation .about-content .p_num {
            width:85%;
        }
    }
    @media screen and (min-width: 800px) {
        .relation .about-content div p {
            padding-left:1% ;
            font-size: 2rem;
            line-height: 3.8rem;
        }
        .relation .about-content .p_float {
            width:13%;
        }
        .relation .about-content .p_num {
            width:85%;
        }
    }
</style>

<div class="container relation" >
    <!-- 主体 -->
    <!-- banner  -->
    <div class="ico">
        <img src="<?= ASSETS_BASE_URI ?>images/relation-ico.png" alt="温都金服地图" >
    </div>
    <!-- 主体 -->
    <div class="about-content row">

        <!--<p class="h-num"><a href="javascript:;" class="number">5</a>资金<span>安全</span></p>-->
        <div class="xinxi"><p class="p_float">公司地址&nbsp;:</p><p class="p_num">温州市鹿城区飞霞南路657号保丰大楼四层</p></div>
        <div class="xinxi"><p class="p_float">工作时间&nbsp;:</p><p class="p_num">9:00-17:00（周一至周六）</p></div>
        <div class="xinxi"><p class="p_float">客服电话&nbsp;:</p><p class="p_num"><?= Yii::$app->params['contact_tel'] ?></p></div>
        <div class="xinxi"><p class="p_float">客服时间&nbsp;:</p><p class="p_num">9:00-20:00（周一至周日，假日例外）</p></div>
        <div class="xinxi"><p class="p_float">客服QQ&nbsp;&nbsp;:</p><p class="p_num">1430843929</p></div>
        <div class="mendian"><p class="p_float">门店地址:</p><p class="p_num">温州市鹿城区飞霞南路657号保丰大楼一层温州都市报（老党校对面）</p></div>
        <div class="xinxi"><p class="p_float">工作时间:</p><p class="p_num">9:00-17:00（周一至周六）</p></div>


        <!--<div class="about-icon"><img src="images/pay.png" alt="合作伙伴"></div>-->
    </div>

</div>

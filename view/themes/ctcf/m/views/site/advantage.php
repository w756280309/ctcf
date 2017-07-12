<?php

$this->title = '平台优势';

?>
<style>
    body {
        background: #fff!important;
        font-size: 62.5%;
    }
    .advan .nav-height {
        margin-bottom: 0!important;
    }
    .advan .ico {
        padding: 0;
        margin: 0;
    }
    .advan .ico img {
        width:100%;
    }
    .advan .about-content {
        margin:30px auto;
        padding-left: 20px;
        padding-right: 20px;
        font-size: 1.4rem;
    }
    .advan .about-content .line {
        padding: 0;
        margin: 0 0 20px;
    }
    .advan .about-content p.h-num {
        margin-top:40px;
        height: 22px;
        line-height: 22px;
        font-size: 1.8rem;
    }

    .advan .h-first {
        clear:both;
        font-size: 1.8rem;
        height: 22px;
        line-height: 22px;
    }
    .advan .about-content p {
        margin:20px 0px 5px 0px;
        text-align: left;
        height: 18px;
        line-height: 18px;
    }

    .advan p span {
        color:#f44336;
    }
    .advan a.number {
        margin-right:7px;
        width:1.7rem;
        height:1.7rem;
        line-height:1.7rem;
        display: inline-block;
        border-radius: 200%;
        background: #f44336;
        color:#fff;
        text-align: center;
    }
    .advan .about-icon {
        margin-top: 40px;
    }
    .advan .about-icon img {
        width:100%;
    }

    .advan .head-line {
        margin: 0 auto 10px;
        width: 100%;
        height: 50px;
        line-height: 50px;
        overflow-x: hidden;
        text-align: center;
        font-size: 1.8rem;
        color:#595757;
    }

    .advan .head-line span {
        font-size:1rem;
        color: #dcdcdc;
    }

    p.suojin {
        padding-left: 1.8em;
    }

    /*iphone6*/
    @media screen and (min-width: 375px) {
        /*.advan .head-line{font-size: 1.3rem;}*/
        .advan .head-line span{font-size:1.3rem;}
    }
    /*iphone6 plus*/
    @media screen and (min-width: 414px) {
        /*.advan .head-line{font-size: 1.8rem;}*/
        .advan .head-line span{font-size:1.6rem;}
    }
    /*ipad */
    @media screen and (min-width: 768px) {
        .advan .head-line{font-size: 3rem;}
        .advan .head-line span{font-size:3rem;}
    }
</style>

<div class="container advan" >
    <!-- 主体 -->
    <!-- banner  -->
    <div class="ico">
        <img src="<?= ASSETS_BASE_URI ?>images/ctcf/ico.jpg" alt="国资平台，值得信赖" >
    </div>
    <!-- 主体 -->
    <div class="about-content row">
        <div class="line">
            <div class="head-line ico"><span>—————</span>楚天财富五大优势<span>—————</span></div>
        </div>

        <p class="h-first"><a href="javascript:;" class="number">1</a><span>国有</span>平台</p>
        <p class="suojin">隶属于湖北省级国有传媒旗下机构，国资平台，值得信赖；</p>
        <p class="h-num"><a href="javascript:;" class="number">2</a><span>股东</span>强势</p>
        <p class="suojin">系湖北日报新媒体集团控股子公司；</p>
        <p class="h-num"><a href="javascript:;" class="number">3</a>资金<span>安全</span></p>
        <p class="suojin">资金全程托管，与平台隔离不被挪用；</p>
        <p class="suojin">绑定本人银行卡，同卡进出，保证本人操作；</p>
        <p class="suojin">层层风控，专业稳健，合法合规；</p>
        <p class="h-num"><a href="javascript:;" class="number">4</a>产品<span>优质</span></p>
        <p class="suojin">主流金融机构产品、优质政府平台类产品、</p>
        <p class="suojin">优质供应链金融产品，期限多样，收益可观；</p>
        <p class="h-num"><a href="javascript:;" class="number">5</a><span>服务</span>便捷</p>
        <p class="suojin">线上有平台，线下有门店，楚天财富在您身边；</p>
        <p class="suojin">千元起投，手机操作，随时随地实现财富增值。</p>
    </div>

</div>

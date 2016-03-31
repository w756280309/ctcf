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
        <img src="<?= ASSETS_BASE_URI ?>images/system/ico.png" alt="国资平台，值得信赖" >
    </div>
    <!-- 主体 -->
    <div class="about-content row">
        <div class="line">
            <div class="head-line ico"><span>—————</span>温都金服五大优势<span>—————</span></div>
        </div>

        <p class="h-first"><a href="javascript:;" class="number">1</a><span>国资</span>背景</p>
        <p class="suojin">隶属温州日报报业集团旗下，国资平台，可信赖；</p>
        <p class="h-num"><a href="javascript:;" class="number">2</a><span>股东</span>强势</p>
        <p class="suojin">温州日报报业集团，实力雄厚的现代国有传媒集团</p>
        <p class="suojin">南京金融资产交易中心，</p>
        <p class="suojin">省级金融主管部门审批的合规平台；</p>
        <p class="h-num"><a href="javascript:;" class="number">3</a>资产<span>优质</span></p>
        <p class="suojin">资金全程托管，与平台隔离不被挪用；</p>
        <p class="suojin">绑定本人银行卡，提现只进本人账户很安全；</p>
        <p class="suojin">层层风控，安全至上；专业稳健，合法合规；</p>
        <p class="h-num"><a href="javascript:;" class="number">4</a><span>灵活</span>便捷</p>
        <p class="suojin">主流金融机构产品、优质政府平台类产品、</p>
        <p class="suojin">优质供应链金融产品，期限多样，收益可观；</p>
        <p class="h-num"><a href="javascript:;" class="number">5</a>资金<span>安全</span></p>
        <p class="suojin">网上有平台，线下有门店，温都金服在您身边</p>
        <p class="suojin">千元起投，手机操作，随时随地实现财富增值。</p>

        <div class="about-icon"><img src="<?= ASSETS_BASE_URI ?>images/system/pay.png" alt="合作伙伴"></div>
    </div>

</div>

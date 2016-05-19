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
    <div class="ico">
        <img src="<?= ASSETS_BASE_URI ?>images/system/ico.png" alt="国资平台，值得信赖" >
    </div>

    <div class="about-content row">
        <div class="line">
            <div class="head-line ico"><span>—————</span>大河金服五大优势<span>—————</span></div>
        </div>

        <p class="h-first"><a href="javascript:;" class="number">1</a>实力背景<span>可信任</span></p>
        <p class="suojin">隶属河南日报报业集团旗下，国资平台规范运营，实力背景可信赖；</p>
        <p class="h-num"><a href="javascript:;" class="number">2</a>合法合规<span>有保证</span></p>
        <p class="suojin">承载大河报全媒体16年信誉，省级金融主管部门审批的合规平台，有保证；</p>
        <p class="h-num"><a href="javascript:;" class="number">3</a>资金托管<span>很安心</span></p>
        <p class="suojin">资金全程第三方托管，用户与平台资金有效隔离；</p>
        <p class="suojin">绑定本人银行卡，资金提现只进本人账户，很安全；</p>
        <p class="h-num"><a href="javascript:;" class="number">4</a>项目优质<span>风险低</span></p>
        <p class="suojin">主流金融机构产品、优质政府平台类项目，风险低；</p>
        <p class="suojin">项目期限有长有短，期限匹配年化收益率，任您选；</p>
        <p class="h-num"><a href="javascript:;" class="number">5</a>操作灵活<span>很便捷</span></p>
        <p class="suojin">线下有服务，网上可交易，咨询、了解触手可及；</p>
        <p class="suojin">门槛低至千元可投，随时随地手机操作，很便捷。</p>
    </div>
</div>

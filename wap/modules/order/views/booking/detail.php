<?php
$this->title = '股权投资';
?>
<link href="<?= ASSETS_BASE_URI ?>css/first.css?v=20160401" rel="stylesheet"/>

<div class="container" style="background: #fff;">

    <div class="row" style="margin:0 auto;">
        <div class="col-xs-12 img-jijin">
            <p class="txt-align-ct">过去十年，是不动产资产为王的时代。</p >
            <p class="txt-align-ct">把握不动产升值趋势的家庭，实现了财富梦想。</p >
            <p class="txt-align-ct marg-top">未来十年，是金融资产为王的时代。</p >
            <p class="txt-align-ct">能够聪明配置金融资产的家庭，将引领财富增长。</p >
            <p class="txt-align-ct">而股权资产，是金融资产中的钻石。</p >
        </div>
    </div>

    <div class="row" style="padding:0; margin:15px 0 50px;" >
        <ul class="col-xs-12" style="padding:0 25px; ">
            <li >
                <div class="color2 p1">
                    <p class="tit"><a href="javascript:;" class="a1" ></a><span class="color1">温股投介绍</span></p>
                    <p>温都金服甄选优秀的股权投资基金管理机构，为市民家庭提供良好的股权投资机会，推出“温股投”系列产品。以私募基金形式，向合资格投资者定向非公开开放。</p>
                </div>
            </li>
            <li >
                <div class="color2 p1">
                    <p class="tit"><a href="javascript:;" class="a1" ></a><span class="color1">投资方向</span></p>
                    <p>第一期温股投产品，投向中国一二线城市的优秀青年白领公寓企业，分享中国城市青年租房需求升级的红利。</p>
                </div>
            </li>
            <li >
                <div class="color2 p1">
                    <p class="tit"><a href="javascript:;" class="a1" ></a><span class="color1">退出方式</span></p>
                    <p>被并购，IPO，股东回购，投资份额转让等。</p>
                </div>
            </li>
            <li >
                <div class="color2 p1">
                    <p class="tit"><a href="javascript:;" class="a1" ></a><span class="color1">投资期限</span></p>
                    <p>2+2年</p>
                </div>
            </li>
            <li >
                <div class="color2 p1">
                    <p class="tit"><a href="javascript:;" class="a1" ></a><span class="color1">起投金额</span></p>
                    <p><?= $model->min_fund ?>万元人民币</p>
                </div>
            </li>
            <li >
                <div class="color2 p1 p2">
                    <p class="tit"><a href="javascript:;" class="a1" ></a><span class="color1">基金管理人</span></p>
                    <p>正规持牌备案，合法合规运行，投资能力卓越。收房租，好生意。温股投，只投稳妥的股权。有意者，请点击预约登记。专业投资机构将派专人为您服务。</p>
                </div>
            </li>


        </ul>
    </div>

    <?php if ($end_flag) { ?>
        <div class="yuyue">预约结束</div>
    <?php } elseif($exist_flag) { ?>
        <div class="yuyue">已预约</div>
    <?php } else { ?>
        <div class="yuyue" style="background-color: #f44336;" onclick="window.location.href='/order/booking/booking?pid=<?= $model->id ?>'">预约</div>
    <?php } ?>

    <div>
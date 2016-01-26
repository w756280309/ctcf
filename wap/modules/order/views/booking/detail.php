<?php
$this->title = '股权投资';
?>

<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/base.css" rel="stylesheet">
<link href="/css/first.css" rel="stylesheet"/>

<div class="container" style="background: #fff;">
        <div class="title-div color1">同信贝湾股权投资资金三期</div>

        <div class="row" style="margin:5px auto;">
            <div class="col-xs-2 col-sm-2" ></div>
            <div class="col-xs-8 col-sm-8 img-jijin">
                <img src="/images/touzi-01.png" alt="" width="100%" height="100" />
            </div>
            <div class="col-xs-2 col-sm-2" ></div>
        </div>

        <div class="row">
            <div class="col-xs-2 col-sm-2" ></div>
            <div class="col-xs-8 col-sm-8 zijin" style="margin:0 auto;padding: 0; text-align: left; line-height: 28px;">
                <ul>
                    <li>
                        <span class="color2">基金规模：</span><span class="color1"><?= $model->total_fund ?>万元</span>
                    </li>
                    <li>
                        <span class="color2">起投金额：</span><span class="color1"><?= $model->min_fund ?>万元</span>
                    </li>
                    <li>
                        <span class="color2">基金周期：</span><span class="color1">2+2 年</span>
                    </li>
                    <li>
                        <span class="color2">基金形式：</span><span class="color1">契约型基金</span>
                    </li>
                </ul>
            </div>
            <div class="col-xs-2 col-sm-2" ></div>
        </div>

        <div class="div1"></div>

        <div class="row" style="padding:0; margin:15px 0 70px;" >
            <ul class="col-xs-12" style="padding:0 25px; ">
                <li >
                    <div class="color2 p1">
                        <a href="javascript:;" class="a1 bg1" ></a>
                        <p><span class="color1">基金管理人:</span>本基⾦管理⼈人为深圳贝湾⾦金融服务有限公司，本基⾦为母基⾦，全额投资于同信投资有限责任公司(西藏同信证券股份有限公司全资券商直投⼦子公司)管理的基⾦</p>
                    </div>
                </li>
                <li >
                    <div class="color2 p1">
                        <a href="javascript:;" class="a1 bg2" ></a>
                        <p><span class="color1">投资方向:</span>本基⾦全额投资于“同信居莫愁股权投资基”该基⾦投资于公寓⾏行业中具有高成长性心价值的拟挂牌新三板的企业</p>
                    </div>
                </li>

                <li >
                    <div class="color2 p1">
                        <a href="javascript:;" class="a1 bg3" ></a>
                        <p><span class="color1">退出方式:</span>同信居莫愁股权投资基金”退出而出，退出方式为挂板新三板退出、IPO 退出、并购退出、原股东回购</p>
                    </div>
                </li>

                <li >
                    <div class="color2 p1">
                        <a href="javascript:;" class="a1 bg4" ></a>
                        <p><span class="color1">管理费:</span>基金管理费为基金投资额的2%</p>
                    </div>
                </li>
                <li >
                    <div class="color2 p1">
                        <a href="javascript:;" class="a1 bg5" ></a>
                        <p><span class="color1">业绩提成:</span>“同信居莫愁股权投资基金二期”年化收益不低于20%的情形下，同信投资有限责任公司按照基金的 10%收取业绩提成，剩余的收益金，本基金年化收益仍不低于 20%的情形下，圳湾金融服务有限公司按照本基金收益10%收取业绩提成</p>
                    </div>
                </li>
            </ul>
        </div>

    <?php if ($end_flag) { ?>
    <div class="yuyue">预约结束</div>
    <?php } elseif($exist_flag) { ?>
    <div class="yuyue">已预约</div>
    <?php } else { ?>
    <div class="yuyue" onclick="window.location.href='/order/booking/booking?pid=<?= $model->id ?>'">预约</div>
    <?php } ?>

<div>
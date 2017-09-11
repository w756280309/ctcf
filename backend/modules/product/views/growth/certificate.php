<?php

use common\utils\StringUtils;

$feUrl = Yii::$app->params['fe_base_uri'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>电子交易资产凭证</title>
    <link rel="stylesheet" href="https://static.wenjf.com/pc/css/base.css">
    <style>
        .container {
            width:750px;
            height:1068px;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }
        .container .print-bg {
            position: absolute;
            top:0;
            left: 0;
            z-index:1;
        }
        .container .inner {
            position: absolute;
            top: 116px;
            left: 0;
            z-index: 2;
            width:100%;
            height:100%;
        }
        .container .inner h4 {
            margin-bottom: 38px;
            width : 100%;
            height : 73px;
            font-size: 48px;
            color:rgb(26,26,26);
            text-align: center;
            font-weight: normal;
        }
        .container .inner .content:after {
            display: block;
            content: '';
            clear: both;
        }
        .container .inner .content span {
            color:rgb(77,77,77);
            font-size: 24px;
        }
        .container .inner .content .lf {
            float: left;
            display: block;
            width: 304px;
            line-height: 30px;
            text-align: right;
        }
        .container .inner .content .rg {
            float: left;
            padding-left: 4px;
            width: auto;
            max-width: 350px;
            line-height: 30px;
            text-align: left;
        }
        .container .inner .organization {
            position: relative;
            float: right;
            right: 110px;
            top: 68px;
            text-align: right;
        }
        .container .inner .organization p {
            line-height: 52px;
            color: #000;
            font-size: 24px;
        }
        .container .inner .organization .time span {
            position: relative;
        }
        .container .inner .organization p .stamps {
            position: absolute;
            top: -106px;
            right: 5px;
            width: 176px;
        }
        .container .inner .bottom-txt:before {
            display: block;
            content: '';
            clear: both;
        }
        .container .inner .bottom-txt {
            position: relative;
            top: 105px;
            left: 0;
        }
        .container .inner .bottom-txt .content .lf {
            width: 282px;
            line-height: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <img class="print-bg" src="<?= $feUrl ?>pc/print-certificate/images/bg-print-img.png" alt="">
    <div class="inner">
        <h4>电子交易资产凭证</h4>
        <div class="content">
            <span class="lf">保全号：</span><span class="rg"><?= $data['ebaoquanId'] ?></span>
        </div>
        <div class="content">
            <span class="lf">保全时间：</span><span class="rg"><?= $data['ebaoquanDate']->format('Y年m月d日') ?></span>
        </div>
        <div class="content">
            <span class="lf">姓名：</span><span class="rg"><?= $data['userName'] ?></span>
        </div>
        <div class="content">
            <span class="lf">证件类型：</span><span class="rg">身份证</span>
        </div>
        <div class="content">
            <span class="lf">证件号码：</span><span class="rg"><?= $data['idcard'] ?></span>
        </div>
        <div class="content">
            <span class="lf">标的名称：</span><span class="rg"><?= $data['title'] ?></span>
        </div>
        <div class="content">
            <span class="lf">产品期限：</span><span class="rg"><?= $data['duration'] ?><br>(实际期限以项目计息后为准)</span>
        </div>
        <div class="content">
            <span class="lf">预期年化收益率：</span><span class="rg"><?= $data['rate'] ?></span>
        </div>
        <div class="content">
            <span class="lf">认购金额：</span><span class="rg"><?= StringUtils::numToRmb($data['orderMoney']) ?>（¥<?= number_format($data['orderMoney'], 2)?>）</span>
        </div>
        <div class="content">
            <span class="lf">还款方式：</span><span class="rg"><?= $data['refundMethod'] ?></span>
        </div>
        <div class="content">
            <span class="lf">认购时间：</span><span class="rg"><?= $data['orderDate']->format('Y年m月d日') ?></span>
        </div>
        <div class="organization">
            <p class="name">温州温都金融信息服务股份有限公司</p>
            <p class="time"><?= $data['date']->format('Y') ?><span>年<img class="stamps" src="<?= $feUrl ?>pc/print-certificate/images/stamps.png" /></span><?= $data['date']->format('m月d日') ?></p>
        </div>
        <div class="bottom-txt">
            <div class="content">
                <span class="lf">客服电话：</span><span class="rg"><?= Yii::$app->params['platform_info.contact_tel'] ?></span>
            </div>
            <div class="content">
                <span class="lf">公司地址：</span><span class="rg">温州市鹿城区飞霞南路657号<br>保丰大楼四层</span>
            </div>
        </div>
    </div>
</div>
</body>
</html>
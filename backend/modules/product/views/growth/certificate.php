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
            font: 24px '宋体';
            background: #fff;
        }
        .container .print-bg {
            position: absolute;
            top:0;
            left: 0;
            margin: 10px 7px 0;
            width: 735px;
            z-index:1;
        }
        .container .inner {
            position: absolute;
            top: 0;
            left: 0;
            padding-top: 94px;
            z-index: 2;
            width:100%;
            height:974px;
        }
        .container .inner h4 {
            margin-bottom: 58px;
            width : 100%;
            height : 73px;
            font: 44px '宋体';
            color:rgb(26,26,26);
            text-align: center;
            font-weight: normal;
        }
        .container .inner .content:after {
            display: block;
            content: '';
            clear: both;
        }
        .container .inner .content {
            padding-left: 100px;
            font-weight: bold;
        }
        .container .inner .content span {
            color:rgb(77,77,77);
            font: 24px '宋体';
        }
        .container .inner .content .left {
            min-width: 98px;
            line-height: 38px;
            color: #444;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .container .inner .content .rig {
            padding-left: 4px;
            width: auto;
            max-width: 480px;
            line-height: 38px;
            font-weight: normal;
            color:rgb(77,77,77);
        }
        .container .inner .content .first-left {
            padding-left: 12px;
        }
        .container .inner .content .name-left {
            padding-left: 50px;
        }
        .container .inner .item-name:after {
            clear: both;
        }
        .container .inner .item-name .left {
            float: left;
            display: block;
        }
        .container .inner .item-name i {
            display: block;
            float: left;
        }
        .container .inner .item-name .rig {
            display: block;
            float: left;
        }
        .container .inner .content-line {
            margin-bottom: 40px;
        }
        .container .inner .organization {
            position: relative;
            float: right;
            right: 54px;
            top: 68px;
            text-align: right;
        }
        .container .inner .organization p {
            line-height: 52px;
            color: #000;
            font-size: 24px;
            font-weight: bold;
        }
        .container .inner .organization .time span {
            position: relative;
        }
        .container .inner .organization p .stamps {
            position: absolute;
            top: -122px;
            right: -7px;
            width: 182px;
            z-index: -1;
        }
        .container .inner .bottom-txt:before {
            display: block;
            content: '';
            clear: both;
        }
        .container .inner .bottom-txt {
            position: absolute;
            bottom: 50px;
            left: 24px;
        }
        .container .inner .bottom-txt .content {
            padding-left: 75px;
            font: 20px '宋体';
            line-height: 26px!important;
            color:rgb(77,77,77)!important;
            font-weight: normal;
        }
    </style>
</head>
<body>
<div class="container">
    <img class="print-bg" src="<?= $feUrl ?>pc/print-product/images/bg-print-img.png" alt="">
    <div class="inner">
        <h4>电子交易资产凭证</h4>
        <div class="content">
            <span class="left">保<i class="first-left">全</i><i class="first-left">号</i></span>：<span class="rig"><?= $data['ebaoquanId'] ?></span>
        </div>
        <div class="content">
            <span class="left">保全时间</span>：<span class="rig"><?= $data['ebaoquanDate']->format('Y年m月d日') ?></span>
        </div>
        <div class="content">
            <span class="left">姓<i class="name-left"></i>名</span>：<span class="rig"><?= $data['userName'] ?></span>
        </div>
        <div class="content">
            <span class="left">证件类型</span>：<span class="rig">身份证</span>
        </div>
        <div class="content content-line">
            <span class="left">证件号码</span>：<span class="rig"><?= $data['idcard'] ?></span>
        </div>
        <div class="content item-name">
            <span class="left ">标的名称</span><i>：</i><span class="rig"><?= $data['title'] ?></span>
        </div>
        <div class="content">
            <span class="left">产品期限</span>：<span class="rig"><?= $data['duration'] ?>(实际期限以项目计息后为准)</span>
        </div>
        <div class="content">
            <span class="left">预期年化收益率</span>：<span class="rig">8.0%</span>
        </div>
        <div class="content">
            <span class="left">认购金额</span>：<span class="rig"><?= StringUtils::numToRmb($data['orderMoney']) ?>（¥<?= number_format($data['orderMoney'], 2)?>）</span>
        </div>
        <div class="content">
            <span class="left">还款方式</span>：<span class="rig"><?= $data['refundMethod'] ?></span>
        </div>
        <div class="content">
            <span class="left">认购时间</span>：<span class="rig"><?= $data['orderDate']->format('Y年m月d日') ?></span>
        </div>
        <div class="organization">
            <p class="name">温州温都金融信息服务股份有限公司</p>
            <p class="time"><?= $data['date']->format('Y') ?><span>年<img class="stamps" src="<?= $feUrl ?>pc/print-product/images/stamps.png" /></span><?= $data['date']->format('m月d日') ?></p>
        </div>
        <div class="bottom-txt">
            <div class="content">客服电话：400-101-5151</div>
            <div class="content">公司地址：温州市鹿城区飞霞南路657号保丰大楼四层
            </div>
        </div>
    </div>
</div>
</body>
</html>
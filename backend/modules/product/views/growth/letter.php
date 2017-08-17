<?php
    $assetUrl = Yii::$app->params['pc_assets_base_uri'];
    $feUrl = Yii::$app->params['fe_base_uri'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>认购确认函</title>
    <link rel="stylesheet" href="https://static.wenjf.com/pc/css/base.css">
    <style>
        body {
            font-family: "宋体";
            font-size: 18px;
        }
        .container {
            width:750px;
            height:1068px;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }
        .confirmation-bg {
            position: absolute;
            top:0;
            left: 0;
            z-index:1;
        }
        .inner {
            position: absolute;
            top:0;
            left: 0;
            z-index: 2;
            width:100%;
            height:100%;
        }
        .container .njfae-logo {
            display: block;
            margin: 75px auto 55px;
        }
        .container h4 {
            font-size:21px;
            color: #333333;
            text-align: center;
            line-height: 32px;
            font-weight: 500;
            margin-bottom: 50px;
            letter-spacing: 5px;
        }
        .container ul {
            padding-left: 62px;
            line-height: 40px;
            color: #333333;
        }
        .special {
            color: #999999;
            text-align: center;
        }
        .timer {
            text-align: right;
            padding-right: 110px;
            margin-top: 38px;
            line-height: 52px;
            color: #333333;
        }
        .stamps {
            position: absolute;
            bottom:105px;
            right:90px;
        }
        .pageBreak{
            page-break-after:always;
            page-break-before: always;
        }
    </style>
</head>
<body>
<?php
$count = count($data);
if ($count > 0) {

    foreach ($data as $key => $value) {
?>
        <div class="container">
            <img class="confirmation-bg" src="<?= $feUrl ?>pc/confirmation-letter/images/confirmation-bg.png" alt="">
            <div class="inner">
                <img class="njfae-logo" src="<?= $feUrl ?>pc/confirmation-letter/images/njfae-logo.png" alt="">
                <h4><?= $value['title']?><br>认购确认函</h4>
                <ul>
                    <li>尊敬的 <?= $value['userName']?>（先生/女士）：</li>
                    <li style="text-indent: 1.5em;">您于<?= (new DateTime($value['orderDate']))->format('Y年m月d日')?>认购了“<?= $value['title']?>”产品，认购信息如下：</li>
                    <li>认购者姓名：<?= $value['userName']?></li>
                    <li>认购者身份证号：<?= $value['idcard']?></li>
                    <li>资金起息日：<?= $value['startDate']->format('Y年m月d日')?></li>
                    <li>产品成立日：<?= $value['fullDate']->format('Y年m月d日')?></li>
                    <li>产品到期日：<?= $value['endDate']->format('Y年m月d日')?></li>
                    <li>期限：<?= $value['duration']?></li>
                    <li>认购金额：<?= \common\utils\StringUtils::numToRmb($value['orderMoney'])?>人民币（<?= number_format($value['orderMoney'], 2)?>元人民币）</li>
                    <li>预期年化收益率：<?= $value['rate']?></li>
                    <li>付息方式：<?= $value['refundMethod']?></li>
                    <li class="special">本产品特此致函确认，并请妥善保管本产品的其他相关文件。</li>
                </ul>
                <p class="timer">温州温都金融信息服务股份有限公司<br><?= (new DateTime($value['date']))->format('Y年m月d日')?></p>
                <img class="stamps" src="<?= $feUrl ?>pc/confirmation-letter/images/stamps.png" />
            </div>
        </div>
<?php
        if ($key !== $count - 1) {
?>
            <div class="pageBreak"></div>
<?php
        }
    }
}
?>
</body>
</html>
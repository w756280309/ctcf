<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>页面出错</title>
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/base.css">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/error.css">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/footer.css">
</head>
<body>
<div class="container">
    <div class="header-nav-box">
        <a href="/">
            <div class="header-logo">
                <img src="<?= ASSETS_BASE_URI ?>images/404/404-logo.png" alt="logo">
            </div>
        </a>
    </div>
    <div class="content">
        <div class="lf content-lf"></div>
        <div class="content-rg">
            <p class="error">抱歉！您访问的页面出错了！<a href="/" class="back-home">回首页></a></p>
            <p class="code-tip">如果您急需我们，请扫描下方二维码。</p>
            <ul class="ul-code">
                <li><a href="javascript:void(0)"><img src="<?= ASSETS_BASE_URI ?>images/404/404-wdjg-code.png" alt="微信订阅号"></a>
                    <p>微信订阅号(项目预告)</p>
                </li>
                <li><a href="javascript:void(0)"><img src="<?= ASSETS_BASE_URI ?>images/404/404-wdjg-codeapp.png" alt="温都金服app"></a>
                    <p>温都金服app</p>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="footer-section footer-section5 footer-fp-auto-height footer-fp-section footer-fp-table">
    <div class="footer-five-box" style="height: 200px">
        <div class="footer-five-address">公司地址：温州市鹿城区飞霞南路657号保丰大楼四层</div>
        <div class="footer-five-tel">客服电话：<span><?= Yii::$app->params['contact_tel'] ?></span><span style="padding-left: 8px;margin-right: 8px;">客服QQ：1430843929</span>客服时间：9:00-20:00（周一至周日）</div>
        <div class="footer-five-partner footer-clearfix"><i>合作伙伴：</i>
            <ul>
                <li style="border-left: 0;padding-left: 0;">温州日报</li>
                <li>温州商报</li>
                <li>温州都市报</li>
                <li>温州晚报</li>
                <li>科技金融时报</li>
                <li>温州网</li>
                <li>温州人杂志</li>
                <li style="border-left:0;padding-left: 0;">南京金融资产交易中心</li>
                <li>同信证券</li>
                <li>联动优势</li>
                <li>电子数据保全中心</li>
            </ul>
        </div>
        <div class="footer-five-copyright">Copyright ©温州温都金融信息服务股份有限公司 浙ICP备16003187号-1</div>
        <div class="footer-first-ma">
            <img src="<?= ASSETS_BASE_URI ?>images/ma.png" alt="">
            <div>访问手机wap版</div>
        </div>
    </div>
</div>
</body>
</html>
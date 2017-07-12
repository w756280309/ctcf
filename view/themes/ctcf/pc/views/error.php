<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>页面出错</title>
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/base.css">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/error.css">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/footer.css">
    <script src="<?= ASSETS_BASE_URI ?>js/jquery-1.8.3.min.js"></script>
    <script src="<?= ASSETS_BASE_URI ?>js/clipboard.min.js"></script>
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
            <br>
            <p class="code-tip">如果您急需我们，请扫描下方二维码。</p>
            <ul class="ul-code">
                <li><a href="javascript:void(0)"><img src="<?= ASSETS_BASE_URI ?>images/ctcf/weixin.jpg" alt="微信订阅号"></a>
                    <p>微信订阅号(项目预告)</p>
                </li>
            </ul>
        </div>
    </div>
</div>

<?= $this->render("@frontend/views/footer.php") ?>
</body>
</html>
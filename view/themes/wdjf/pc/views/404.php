<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404</title>
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/base.css">
    <link rel="stylesheet" href="<?= ASSETS_BASE_URI ?>css/404.css">
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
        <div class="rg content-rg">
            <p class="error">抱歉！当前正有大批理财者进入，您暂时无法访问此页面！<a href="/" class="back-home">回首页></a></p>
            <p class="code-tip">如果您急需我们，请扫描下方二维码。</p>
            <ul class="ul-code">
                <li><a href="javascript:void(0)"><img src="<?= ASSETS_BASE_URI ?>images/404/404-wdjg-code.png" alt="微信订阅号"></a>
                    <p>微信订阅号(项目预告)</p>
                </li>
                <li><a href="javascript:void(0)"><img src="<?= ASSETS_BASE_URI ?>images/404/404-wdjg-codeapp.png" alt="温都金服app"></a>
                    <p>温都金服APP</p>
                </li>
            </ul>
        </div>
    </div>
</div>
<?= $this->render("@frontend/views/footer.php") ?>
</body>
</html>

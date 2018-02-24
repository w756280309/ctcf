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
    <div class="content">
        <div class="lf content-lf"></div>
        <div class="content-rg">
            <p class="error">抱歉！您访问的页面出错了！<a href="/" class="back-home">回首页></a></p>
            <br>
            <p class="code-tip">如果您急需我们，请扫描下方二维码。</p>
            <ul class="ul-code">
                <li><a href="javascript:void(0)"><img width="132" src="<?= ASSETS_BASE_URI ?>ctcf/images/weixin_follow.png" alt="官方微信"></a>
                    <p>官方微信</p>
                </li>
                <li><a href="javascript:void(0)"><img width="132" src="<?= ASSETS_BASE_URI ?>ctcf/images/app_download.png" alt="下载APP"></a>
                    <p>下载APP</p>
                </li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
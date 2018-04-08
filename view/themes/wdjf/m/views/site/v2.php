<?php

$this->title = 'APP下载提示';
$this->hideHeaderNav = false;
?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170919">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<style>
    body {
        background-color: #ff5970;
    }
    .flex-content {
        padding-bottom: 0.66666667rem;
    }
    .flex-content .picture {
        height: 4.06666667rem;
        width: 100%;
    }
    .flex-content .picture img {
        width: 100%;
        height: 100%;
        display: block;
    }
    .flex-content .link {
        width: 7.17333333rem;
        height: 1.21333333rem;
        display: block;
        margin: 0 auto;
    }
    .flex-content .link img {
        width: 100%;
        height: 100%;
        display: block;
    }
    .flex-content .tips {
        color: #fff;
        font-size: 0.4rem;
        text-align: center;
        line-height: 0.73333333rem;
        margin-top: 0.22666667rem;
    }

</style>
<div class="flex-content">
    <div class="picture"><img onclick="prevent();" src="<?= FE_BASE_URI ?>wap/download-page/images/wd_banner_01.png" alt=""></div>
    <div class="picture"><img onclick="prevent();" src="<?= FE_BASE_URI ?>wap/download-page/images/wd_banner_02.png" alt=""></div>
    <div class="picture"><img onclick="prevent();" src="<?= FE_BASE_URI ?>wap/download-page/images/wd_banner_03.png" alt=""></div>
    <a class="link" href="http://a.app.qq.com/o/simple.jsp?pkgname=com.wz.wenjf"><img src="<?= FE_BASE_URI ?>wap/download-page/images/btn.png" alt=""></a>
    <p class="tips">积分商城暂时无法在微信端正常访问，<br>我们建议您下载温都金服APP，以便正常使用。</p>
</div>
<script type="text/javascript">
    function prevent(event) {
        var event = event || window.event;
        event.preventDefault();
    }
</script>
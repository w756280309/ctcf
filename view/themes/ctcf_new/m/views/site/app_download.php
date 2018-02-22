<?php

$this->title = $title;
$this->share = $share;
$this->headerNavOn = true;

?>

<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css?v=20170906">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<style>
    body{background-color: #fff}
    .flex-content{
        width: 100%;
        height: 100%;
        padding-top:2.82666667rem;
    }
    .flex-content .download-pic{
        display: block;
        width:3.72rem;
        margin:0 auto 1.06666667rem;
    }
    .flex-content .download-content{
        line-height:0.64rem;
        font-size: 0.4rem;
        text-align: center;
        color: #999;
    }
    .flex-content .download-button{
        display: block;
        width:5.49333333rem;
        height:1.29333333rem;
        line-height:1.29333333rem;
        margin:1.10666667rem auto 0;
        -webkit-border-radius:8px;
        -moz-border-radius:8px;
        border-radius:8px;
        color: #fff;
        background-color: #f54336;
        font-size: 0.45333333rem;
        text-align: center;
    }
</style>

<div class="flex-content">
    <img src="<?= FE_BASE_URI ?>wap/app-download/images/pic_download.png" alt="" class="download-pic">
    <p class="download-content">为了更好的用户体验</p>
    <p class="download-content">请您通过楚天财富App参与活动</p>
    <p class="download-content">感谢您的理解与支持哦！</p>
    <a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.wz.wenjf" class="download-button">下载APP</a>
</div>

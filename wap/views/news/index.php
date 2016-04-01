<?php
$this->title = '最新资讯';
?>
<link href="<?= ASSETS_BASE_URI ?>css/news.css" rel="stylesheet">

<!-- 主体 -->
<div class="information-list">
        <a href="/news/detail?id=1" class="single">
                <p class="single-title">【资讯标题】温都金服定于4月6日试上线</p>
                <p class="single-description">温州报业传媒旗下理财平台—温都金服（wenjf.com），定于4月6日试上线。</p>
                <p class="single-time">2016-4-5</p>
        </a>
        <a href="/news/detail?id=2" class="single">
                <p class="single-title">【资讯标题】用户资金托管引入联动优势</p>
                <p class="single-description">温都金服引入联动优势（由中国银联与中国移动联合发起成立）提供用户资金托管综合服务。</p>
                <p class="single-time">2016-4-5</p>
        </a>
        <!--加载跟多-->
        <div class="load" style="display:block;"></div>
</div>


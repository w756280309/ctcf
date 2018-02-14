<?php
    $this->title = '登录/注册';
?>
<style>
    .guide-login {
        width:1000px;
        height:408px;
        margin: 69px auto 160px;
        background-color: #fff;
        border: 1px solid #e3e3e3;
    }
    .guide-login h5 {
        font-size:20px;
        color: #1a1f25;
        padding-left:30px;
        line-height: 48px;
        border-bottom:1px solid #dadada;
    }
    .guide-login dl {
        padding:170px 52px 0 154px;
    }
    .guide-login dl dt {
        width:410px;
        padding-right:40px;
        font-size:24px;
        color: #999999;
        line-height: 52px;
    }
    .guide-login dl dd a{
        font-size:20px;
        color: #fff;
        width:240px;
        height:52px;
        line-height:52px;
        display: block;
        background-color: #ff6707;
        text-align: center;
    }
    .clearfix:after {
        content: "";
        display: block;
        clear: both;
        visibility: hidden;
        font-size: 0;
        height: 0;
    }

</style>
<!--仅仅是中间内容区-->
<div class="guide-login">
    <h5>您还未登录</h5>
    <dl class="clearfix">
        <dt class="lf">浏览产品前请先登录哦！</dt>
        <dd class="lf"><a href="/site/login">立即登录</a></dd>
    </dl>
</div>

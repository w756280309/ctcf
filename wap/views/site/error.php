<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */
$this->registerMetaTag([
    'HTTP-EQUIV' => 'REFRESH',
    'CONTENT' => "5 ; URL=/",
]);
$this->title = $name;
$this->registerCss("
    html, .container{
        background-color: #fff;
    }
    .flex-content{background: #fff}
    .flex-content > img{
        display: block;
        width:8.4rem;
        height:7.306rem;
        margin: 0.8rem auto 0.8533rem;
    }
    .flex-content > p{
        font-family:'宋体';
        line-height:1.2rem;
        margin: 0 auto;
        font-size:16px;
        text-align: center;

    }
    .flex-content > .link-404{
        display: block;
        height:1.2rem;
        line-height:1.2rem;
        width:4.933rem;
        margin: 0 auto;
        text-align: center;
        border-radius: 8px;
        background-color: #f1453d;
        color: #fff;
    }
");
?>
<link rel="stylesheet" href="<?= FE_BASE_URI ?>wap/common/css/wenjfbase.css">
<script src="<?= FE_BASE_URI ?>libs/lib.flexible3.js"></script>
<div class="flex-content">
    <img src="<?= FE_BASE_URI ?>wap/page-404/images/pic_404.png" alt="">
    <p>亲爱的用户,您访问的页面不存在或已失效</p>
    <p>该页将在 <span id='setouttime'>5</span>秒后自动跳转!</p>
    <a class="link-404 f17" href="/">返回首页</a>

</div>
<script type="text/javascript">
    self.setInterval("countdown()",1000);
    function countdown(){
        var t;
        t = document.getElementById("setouttime").innerHTML;
        t > 0 ? t-- : t;
        document.getElementById("setouttime").innerHTML=t;
    }
</script>

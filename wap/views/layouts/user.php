<?php
use yii\helpers\Html;
use wap\assets\WapAsset;
WapAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- 设置IE浏览器的解析模式-->
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" >
    <meta name="renderer" content="webkit">
    <!--视窗设置 -->
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title></title>

    <!-- Bootstrap -->
    <!--1、加载Bottstrap层叠样式表 -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/base.css" rel="stylesheet">
    <!-- 加载Bootstrap的样式文件-->

<!--    <script src="/js/TouchSlide.1.1.js"></script>-->
    <script src="/js/jquery.js"></script>
    <script src="/js/common.js"></script>
    <script>
            $(function(){
                 $('.back img').bind('click',function(){
                      history.go(-1);
                 })
            })
    </script>
</head>
<body>
    <?php $this->beginBody() ?>

    <!--标的详情页头部 start-->
     <div class="container">
            <div class="row title-box nav-height">
                <div class="col-xs-2 back"><img src="/images/back.png" alt=""/></div>
                <div class="col-xs-8 title"><?=$this->title ?></div>
                <div class="col-xs-2 back"></div>
            </div>
    <!--标的详情页头部 end-->
		<?= $content ?>


    <?php $this->endBody() ?>


</body>
</html>
<?php $this->endPage() ?>




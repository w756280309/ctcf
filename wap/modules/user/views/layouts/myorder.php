<?php
use common\view\BaiduTongjiHelper;

frontend\assets\WapAsset::register($this);
BaiduTongjiHelper::registerTo($this, BaiduTongjiHelper::WAP_KEY);
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
    <title>温都金服</title>

    <!-- Bootstrap -->
    <!--1、加载Bottstrap层叠样式表 -->
    <link href="<?= ASSETS_BASE_URI ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= ASSETS_BASE_URI ?>css/base.css" rel="stylesheet">
    <!-- 加载Bootstrap的样式文件-->

    <!-- 使得IE8也适合HTML5的元素和媒体查询-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!-- 如果是IE9以下的版本，就引入下面两个JS文件-->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<!--    <script src="/js/TouchSlide.1.1.js"></script>-->
    <script src="<?= ASSETS_BASE_URI ?>js/jquery.js"></script>
    <script src="<?= ASSETS_BASE_URI ?>js/common.js"></script>
    <script>
        $(function(){
             $('.back img').bind('click',function(){
                  window.location.href='/user/user';
             })
        })
    </script>
</head>
<body>
    <?php $this->beginBody() ?>

    <!--标的详情页头部 start-->
     <div class="container">
        <div class="row title-box nav-height">
            <div class="col-xs-2 back"><img src="<?= ASSETS_BASE_URI ?>images/back.png" alt=""/></div>
            <div class="col-xs-8 title"><?=$this->title ?></div>
            <div class="col-xs-2 back"></div>
        </div>
        <!--标的详情页头部 end-->
	<?= $content ?>
     </div>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
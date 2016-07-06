<?php

use yii\helpers\Html;
use common\models\AuthSys;

$menus = AuthSys::getMenus();
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>
        <link href="/css/default.css" rel="stylesheet" type="text/css" id="style_color"/>
        <link href="/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <script src="/js/jquery-1.10.1.min.js" type="text/javascript"></script>

        <script type="text/javascript" src="/js/layer/layer.min.js"></script>
        <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
        <script type="text/javascript" src="/js/showres.js"></script>
        <script type="text/javascript" src="/js/JPlaceholder.js"></script>
        <style type="text/css">
            .portlet-body .title{
                height: 34px;
                line-height: 34px;
                vertical-align: middle;
                font-size: 14px;
            }
            .has-error .help-block{
                color: red;
            }
            .form-group div.radio label{
                width:400px;
            }

            <?php if (Yii::$app->params['ui_nav_bgcolor_hex']): ?>
                .header .navbar-inner {
                    background-color: <?= Yii::$app->params['ui_nav_bgcolor_hex'] ?> !important;
                }
                .navbar-inverse .nav>li>a {
                    color: #fff;
                }
            <?php endif; ?>
        </style>
        <script type="text/javascript">
            $(function(){
                $('form .radio').each(function(k,o){
                    if(k/2==0){
                        $($('form .radio').get(k)).css({width:'100px'});
                    }
                })
            })
        </script>
    </head>
    <body class="page-header-fixed">
    <?php $this->beginBody() ?>
        <div class="header navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <a class="brand" href="/">
                        <img src="<?= Yii::$app->params['ui_main_logo'] ?>" alt="logo">
                    </a>

                    <div class="navbar hor-menu hidden-phone hidden-tablet">
                        <div class="navbar-inner">
                            <ul class="nav">
                                <li><a href="/">首页</a></li>
                                <?php  foreach ($menus as $val){ ?>
                                    <li><a href="/<?=$val['path']?>"><?=$val['auth_name']?></a></li>
                                <?php  } ?>
                            </ul>
                        </div>
                    </div>

                    <a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
                        <img src="/image/menu-toggler.png" alt="">
                    </a>

                    <ul class="nav pull-right">
                        <li class="dropdown user">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="username"><?= Yii::$app->user->getIdentity()->username ?></span>
                                <i class="icon-angle-down"></i>
                            </a>

                            <ul class="dropdown-menu">
                                <li><a href="javascript:void(0)" onclick="openwin('/adminuser/admin/editpass',500,300)"><i class="icon-user"></i> 修改密码</a></li>
                                <li class="divider"></li>
                                <li><a href="/login/logout"><i class="icon-key"></i> Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-container row-fluid">
            <!-- BEGIN HORIZONTAL MENU PAGE SIDEBAR1 -->
            <div class="page-sidebar nav-collapse collapse">
                    <?= $this->blocks['block1eft'] ?>
            </div>
            <!-- END BEGIN HORIZONTAL MENU PAGE SIDEBAR -->

            <!-- BEGIN PAGE -->
            <div class="page-content">
                <?= $this->blocks['blockmain'] ?>
            </div>
            <!-- END PAGE -->
	</div>
<?php $this->endBody() ?>
        <!-- END FOOTER -->
        <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- BEGIN CORE PLUGINS -->

        <script src="/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
        <!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
        <script src="/js/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
        <script src="/js/bootstrap.min.js" type="text/javascript"></script>
        <!--[if lt IE 9]>
        <script src="/js/excanvas.min.js"></script>
        <script src="/js/respond.min.js"></script>
        <![endif]-->
        <script src="/js/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="/js/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="/js/jquery.cookie.min.js" type="text/javascript"></script>
        <script src="/js/jquery.uniform.min.js" type="text/javascript" ></script>
        <!-- END CORE PLUGINS -->
        <script src="/js/app.js"></script>
        <script>
            jQuery(document).ready(function() {
                App.init();
            });
        </script>
        <!-- END JAVASCRIPTS -->
    </body>
</html>
<?php $this->endPage() ?>
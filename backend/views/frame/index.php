<?php
/* @var $this yii\web\View */

$this->title = 'Show Main';
?>
<?php $this->beginBlock('block1eft'); ?>
<ul class="page-sidebar-menu hidden-phone hidden-tablet">
        <li>
                <div class="sidebar-toggler hidden-phone"></div>
        </li>
        <li>
                <a href="javascript:;">
                <i class="icon-th-list"></i> 
                <span class="title">管理首页</span>
                <span class="arrow "></span>
                </a>
                
        </li>
</ul>
<?php $this->endBlock(); ?>



<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">

        <div class="span12">
            
                <h3 class="page-title">
                        欢迎使用温都金服后台管理系统
                </h3>
        </div>
    </div>
</div>
<?php $this->endBlock(); ?>


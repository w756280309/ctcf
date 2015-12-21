<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use common\models\AuthSys;
$menus = AuthSys::getMenus('A100000');
?>

<?php $this->beginBlock('block1eft'); ?>
<ul class="page-sidebar-menu hidden-phone hidden-tablet">
        <li>
                <div class="sidebar-toggler hidden-phone"></div>
        </li>
        <li class="open">
                <a href="javascript:;">
                <i class="icon-th-list"></i> 
                <span class="title">菜单列表</span>
                <span class="arrow "></span>
                </a>
            <ul class="sub-menu" style="display: block">
<!--                        <li>
                                <a href="/adv/adv/index">广告管理</a>
                        </li>
                        <li>
                                <a href="/adv/adv/index">首页轮播</a>
                        </li>
                        <li>
                                <a href="/adv/adv/edit">添加首页轮播</a>
                        </li>-->
                          <?php foreach ($menus as $val){ ?>
                            <li><a href="/<?=$val['path']?>" target="_self"><?=$val['auth_name']?></a></li>
                        <?php } ?>
                </ul>
        </li>
</ul>
<?php $this->endBlock(); ?>

<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use common\models\AuthSys;
$menus = AuthSys::getMenus('N1000000');
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
                        <li>
                                <a href="/user/user/list">产品列表</a>
                        </li>
                        <li>
                                <a href="/user/user/list">项目列表</a>
                        </li>
                        <li>
                                <a href="/user/user/list">放款列表</a>
                        </li>
            </ul>
        </li>
</ul>
<?php $this->endBlock(); ?>

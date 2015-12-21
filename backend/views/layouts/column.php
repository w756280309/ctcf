<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use common\models\AuthSys;

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

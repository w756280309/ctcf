<?php
/* @var $this yii\web\View */
use common\models\AuthSys;
$menus = AuthSys::getMenus('P1000000');

$this->title = 'Show Main';
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">

        <div class="span12">
            
                <h3 class="page-title">
                        默认首页 <small></small>
                </h3>
            
        </div>

    </div>
                                    
</div>
<?php $this->endBlock(); ?>


<?php
/* @var $this yii\web\View */

$this->title = 'Show Main';
use common\models\AuthSys;
$menus = AuthSys::getMenus('S1000000');
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    
    <div class="row-fluid">
        <div class="span12">            
                <h3 class="page-title">
                        默认系统管理首页 <small></small>
                </h3>            
        </div>
    </div>
                                    
</div>
<?php $this->endBlock(); ?>

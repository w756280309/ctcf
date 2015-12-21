<?php
/* @var $this yii\web\View */

$this->title = 'Show Main';
use common\models\AuthSys;
$menus = AuthSys::getMenus('N1000000');
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">

    <!-- BEGIN PAGE HEADER-->

    <div class="row-fluid">

        <div class="span12">
            
                <h3 class="page-title">

                        Horzontal &amp; Sidebar Menu <small>horizontal &amp; sidebar menu layout</small>

                </h3>

                <ul class="breadcrumb">

                        <li>

                                <i class="icon-home"></i>

                                <a href="index.html">Home</a> 

                                <i class="icon-angle-right"></i>

                        </li>

                        <li>

                                <a href="#">Layouts</a>

                                <i class="icon-angle-right"></i>

                        </li>

                        <li><a href="#">Horzontal &amp; Sidebar Menu</a></li>

                </ul>
            
        </div>

    </div>
                                    
</div>
<?php $this->endBlock(); ?>


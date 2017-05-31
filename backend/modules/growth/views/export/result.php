<?php

$this->title = '导出类型选择';
$this->registerJsFile('/js/My97DatePicker/WdatePicker.js', ['depends' => 'yii\web\YiiAsset']);
?>

<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                数据导出 <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/growth/export/index">导出类型选择</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="#">下载等待页</a>
                </li>
            </ul>
        </div>
        <?php if($fileExists) { ?>
             <?= $exportModel['title']?>下载 <a href="/growth/export/download?sn=<?= $sn?>" target="_blank"><?= $sn . '.xlsx'?></a>
        <?php } else { ?>
            <div class="span12 alert alert-info">
                后台程序正在下载数据, 请稍后刷新页面
            </div>
        <?php }?>
    </div>
</div>
<?php $this->endBlock(); ?>


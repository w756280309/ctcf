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
        <div id="result">
            <div id="downloan-link" style="display:<?= $fileExists ? 'block' : 'none'?>">
                <?= $title?>下载 <a href="/growth/export/download?sn=<?= $sn?>" target="_blank"><?= $sn . '.xlsx'?></a>
            </div>
            <div class="span12 alert alert-info" id="alter-info" style="display:<?= !$fileExists ? 'block' : 'none'?>">
                后台程序正在下载数据, 请稍后刷新页面
            </div>
            <?php if(!$fileExists) { ?>
                <script>
                    var t =  t = setInterval(function () {
                        getResult();
                    }, 1000);
                    getResult();
                    function getResult() {
                        var url = '<?= Yii::$app->request->absoluteUrl?>';
                        $.get(url,function(data) {
                            console.log(data);
                            if (data.fileExists) {
                                $('#downloan-link').show();
                                $('#alter-info').hide();
                                clearInterval(t);
                            }
                        })
                    }
                </script>
            <?php } ?>
        </div>

    </div>
</div>
<?php $this->endBlock(); ?>


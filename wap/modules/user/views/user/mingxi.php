<?php
$this->title = "交易明细";
$this->registerJsFile('/js/jiaoyimingxi.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
frontend\assets\WapAsset::register($this);
$this->registerJs('var total=' . $header['count'] . ';', 1);
$this->registerJs('var size=' . $header['size'] . ';', 1);
$this->registerJs('var tp=' . $header['tp'] . ';', 1);
$this->registerJs('var cp=' . $header['cp'] . ';', 1);
?>
<link rel="stylesheet" href="/css/base.css"/>
<link rel="stylesheet" href="/css/jiaoyimingxi.css"/>
<div class="container" style="background: #fff;padding-top: 10px;padding-bottom:10px;">
    <div class="row jiaoyi">
        <div class="col-xs-3">时间</div>
        <div class="col-xs-3">用途</div>
        <div class="col-xs-3">金额(元)</div>
        <div class="col-xs-3">账户余额(元)</div>
    </div>
    <?php if ($model) {
        foreach ($model as $val): ?>
            <div class="clear"></div>
            <div class="row md-height border-bottom">
                <div class="col-xs-3 data">
                    <span class="data1"><?= $val['created_at_date'] ?></span>
                    <span class="data2"><?= $val['created_at_time'] ?></span>
                </div>
                <div class="col-xs-3 revenue"><?= $val['type'] ?></div>
                <div class="col-xs-3 money"><?= ($val['in_money']>$val['out_money'])?('+'.$val['in_money']):('-'.$val['out_money']) ?></div>
                <div class="col-xs-3 revenue"><?= $val['balance'] ?></div>
            </div>
        <?php endforeach; ?>
        <div class="load">加载更多</div>
    <?php } else { ?>
        <div class="nodata">暂无数据</div>
<?php } ?>
</div>

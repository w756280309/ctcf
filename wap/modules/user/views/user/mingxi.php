<?php
$this->title = "交易明细";

$this->registerJs('var mingxitype = new Array();', 1);
foreach (Yii::$app->params['mingxi'] as $key => $val) {
    $this->registerJs('mingxitype['.$key.'] = \''.$val.'\';', 1);
}
$this->registerJsFile('/js/moment.min.js', ['depends' => 'yii\web\YiiAsset','position' => 1]);
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
                    <span class="data1"><?= date('Y-m-d', $val['created_at']) ?></span>
                    <span class="data2"><?= date('H:i:s', $val['created_at']) ?></span>
                </div>
                <div class="col-xs-3 revenue"><?= Yii::$app->params['mingxi'][$val['type']] ?></div>
                <div class="col-xs-3 money"><?= ($val['in_money']>$val['out_money'])?('+'.$val['in_money']):('-'.$val['out_money']) ?></div>
                <div class="col-xs-3 revenue"><?= $val['balance'] ?></div>
            </div>
        <?php endforeach; ?>
        <div class="load" style="display:block;">加载更多</div>
    <?php } else { ?>
        <div class="nodata" style="display:block;">暂无数据</div>
<?php } ?>
</div>
